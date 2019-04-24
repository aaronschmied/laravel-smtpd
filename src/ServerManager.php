<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 11:57
 */

namespace Smtpd;


use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Logger;
use Psr\Log\LoggerInterface;
use Smtpd\Auth\GuardHandler;
use Smtpd\Auth\Handler;
use Smtpd\Contracts\AuthorizesRecipients;
use Smtpd\Events\MessageRecieved;
use Smtpd\Smtp\Event;
use Smtpd\Smtp\Server;

class ServerManager
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AuthorizesRecipients
     */
    protected $recipientHandler;

    /**
     * @var Handler
     */
    protected $authHandler;

    /**
     * @var Server
     */
    protected $server;

    /**
     * Server constructor.
     *
     * @param Application $app
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->config = $app->get('config');

        $this->recipientHandler = $app->make($this->config->get('smtpd.auth.authorize_recipients'));

        $this->logger = $app->make(Logger::class);

        if ($handler = $this->config->get('smtpd.auth.handler')) {
            $this->authHandler = $app->make($handler);
        }
        $this->authHandler = new GuardHandler($this->config->get('smtpd.auth.guard', 'smtp'));
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get the config for the server
     *
     * @return array
     */
    protected function buildConfig(): array
    {
        return [
            'ip' => $this->config->get('smtpd.interface'),
            'port' => $this->config->get('smtpd.port'),
            'hostname' => $this->config->get('smtpd.hostname'),
            'logger' => $this->logger,
        ];
    }

    /**
     * Get the context options.
     *
     * @return array
     */
    protected function buildContext(): array
    {
        if ($this->config->has('smtpd.context_options')) {
            return $this->config->get('smtpd.context_options');
        }

        return [];
    }

    /**
     * Creates the server instance.
     *
     * @return Server
     *
     * @throws \Exception
     */
    protected function makeServer()
    {
        $this->server = new Server($this->buildConfig());

        if (!$this->server->listen($this->buildContext())) {
            throw new \Exception('SMTP Server could not listen on selected interface');
        }

        $this->server->addEvent(new Event(
                Event::TRIGGER_AUTH_ATTEMPT,
                $this,
                'handleAuthAttempt'
                          ));

        $this->server->addEvent(new Event(
            Event::TRIGGER_NEW_MAIL,
            $this,
            'handleNewMail'
                          ));

        $this->server->addEvent(new Event(
            Event::TRIGGER_NEW_RCPT,
            $this,
            'handleNewRecipient'
                          ));

        return $this->server;
    }

    /**
     * Run the server.
     *
     * @throws \Exception
     */
    public function run()
    {
        $this
            ->makeServer()
            ->loop();
    }

    /**
     * Handle an auth attempt.
     *
     * @param Event  $event
     * @param string $method
     * @param array  $credentials
     *
     * @return bool
     */
    public function handleAuthAttempt(Event $event, string $method, array $credentials)
    {
        try {
            switch ($method) {
                case 'login':
                    return ! is_null($this->eventUser($event));
                default:
                    throw new \Exception("Unsupported auth method '{$method}'.");
            }
        } catch (\Exception $exception) {
            $this
                ->logger
                ->critical('Error while trying to authenticate.', compact('exception'));
        }

        return false;
    }

    /**
     * Handle a new recipient added.
     *
     * @param Event  $event
     * @param string $recipient
     *
     * @return bool
     */
    public function handleNewRecipient(Event $event, string $recipient)
    {
        if (!$this->recipientHandler) {
            return false;
        }

        return $this
            ->recipientHandler
            ->authorize($this->eventUser($event), $recipient);
    }

    /**
     * Handle an incoming message.
     *
     * @param Event              $event
     * @param string             $from
     * @param array              $recipients
     * @param \Zend\Mail\Message $message
     *
     * @return bool
     */
    public function handleNewMail(Event $event, string $from, array $recipients, \Zend\Mail\Message $message)
    {
        MessageRecieved::dispatch($this->eventUser($event), MessageFactory::make($message, $from, $recipients));
    }

    /**
     * Try to get the user from an event.
     *
     * @param Event $event
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    private function eventUser(Event $event)
    {
        $credentials = $this
            ->authHandler
            ->decodeCredentials(
                $event
                    ->getClient()
                    ->getCredentials()
            );

        return $this
            ->authHandler
            ->attempt($credentials);
    }
}
