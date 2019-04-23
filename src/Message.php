<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:00
 */

namespace Smtpd;

use Zend\Mail\Message as ZendMessage;
use Illuminate\Mail\Mailable;

class Message extends Mailable
{
    /**
     * @var ZendMessage?
     */
    protected $zendMessage;

    /**
     * Set the zend message.
     *
     * @param ZendMessage $zendMessage
     *
     * @return $this
     */
    public function setZendMessage(ZendMessage $zendMessage)
    {
        $this->zendMessage = $zendMessage;

        return $this;
    }

    /**
     * Get the zend message.
     *
     * @return ZendMessage|null
     */
    public function getZendMessage(): ?ZendMessage
    {
        return $this->zendMessage;
    }

    /**
     * Get the raw message content.
     *
     * @return string
     */
    public function getRawMessage()
    {
        return $this->getZendMessage() ? $this
            ->getZendMessage()
            ->toString() : '';
    }
}
