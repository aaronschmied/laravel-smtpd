<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 11:46
 */

namespace Smtpd\Commands;


use Illuminate\Console\Command;
use Smtpd\Logging\CommandLogProxy;
use Smtpd\ServerManager;

class Listen extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smtpd:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for incoming smtp connections.';

    /**
     * @var ServerManager
     */
    protected $serverManager;

    /**
     * Listen constructor.
     *
     * @param ServerManager $serverManager
     */
    public function __construct(ServerManager $serverManager)
    {
        $this->serverManager = $serverManager;
        parent::__construct();
    }

    /**
     * Get the server manager.
     *
     * @return ServerManager
     */
    private function getServerManager()
    {
        return $this->serverManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        $this
            ->getServerManager()
            ->setLogger(new CommandLogProxy($this))
            ->run();
    }
}
