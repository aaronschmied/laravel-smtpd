<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 15:06
 */

namespace Smtpd\Logging;


use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

class CommandLogProxy implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var Command
     */
    protected $command;

    /**
     * @var bool
     */
    protected $verbose = false;

    /**
     * CommandOutLogger constructor.
     *
     * @param Command $command
     * @param bool    $verbose
     */
    public function __construct(Command $command, bool  $verbose = false)
    {
        $this->command = $command;
        $this->verbose = $verbose;
    }

    /**
     * Log a message to the command
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        if (!empty($context)) {
            $message .= " " . json_encode($context);
        }

        switch ($level) {
            case LogLevel::EMERGENCY:
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::ERROR:
                $this->command->error($message);
                break;
            case LogLevel::WARNING:
                $this->command->warn($message);
                break;
            default:
                if ($this->verbose) {
                    $this->command->info($message);
                }
        }
    }
}
