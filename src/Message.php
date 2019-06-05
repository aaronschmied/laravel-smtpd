<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:00
 */

namespace Smtpd;

use Illuminate\Contracts\Filesystem\Filesystem;
use Zend\Mail\Message as ZendMessage;

class Message
{
    /**
     * @var string
     */
    protected $raw;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $to;

    /**
     * Message constructor.
     *
     * @param string $raw
     * @param string $from
     * @param array $to
     */
    public function __construct($raw, $from, $to)
    {
        $this->raw = $raw;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Store the raw message in a given filesystem.
     *
     * @param Filesystem $filesystem
     * @param null       $path
     * @param array      $options
     *
     * @return string|null
     */
    public function store(Filesystem $filesystem, $path = null, $options = [])
    {
        if (is_null($path)) {
            $path = uniqid('msg_') . ".eml";
        }
        $filesystem->put($path, $this->getRaw(), $options);
        return $path;
    }

    /**
     * Get the raw message content.
     *
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * Create a mailable from the message.
     *
     * @return Mailable
     */
    public function makeMailable(): Mailable
    {
        return (new Mailable())
            ->setZendMessage($this->getZendMessage());
    }

    /**
     * Get the zend message.
     *
     * @return ZendMessage
     */
    public function getZendMessage(): ZendMessage
    {
        return ZendMessage::fromString($this->raw)
            ->setFrom($this->from)
            ->setTo($this->to);
    }
}
