<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 11:45
 */

namespace Smtpd\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Smtpd\Message;

class MessageRecieved
{
    use Dispatchable, SerializesModels;

    /**
     * @var Authenticatable?
     */
    public $user;

    /**
     * @var Message
     */
    public $message;

    /**
     * MessageRecieved constructor.
     *
     * @param Authenticatable|null  $user
     * @param Message               $message
     */
    public function __construct(?Authenticatable $user, Message $message)
    {
        $this->user = $user;
        $this->message = $message;
    }
}
