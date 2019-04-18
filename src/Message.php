<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:00
 */

namespace Smtpd;




use Illuminate\Mail\Mailable;

class Message extends Mailable
{
    /**
     * @var \Zend\Mail\Message
     */
    protected $zendMessage;

    /**
     * Set the zend message.
     *
     * @param \Zend\Mail\Message $zendMessage
     *
     * @return $this
     */
    public function setZendMessage(\Zend\Mail\Message $zendMessage)
    {
        $this->zendMessage = $zendMessage;

        return $this;
    }

    /**
     * Create a new message instance from the given zend message
     *
     * @param \Zend\Mail\Message $message
     * @param string             $from
     * @param array              $recipients
     *
     * @return Message
     */
    public static function makeFrom(\Zend\Mail\Message $message, string $from, array $recipients)
    {
        return (new static())
            ->from($from)
            ->to($recipients)
            ->html($message->getBody())
            ->setZendMessage($message);
    }
}
