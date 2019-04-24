<?php
/**
 * Created by PhpStorm.
 * User: aaronschmied
 * Date: 2019-04-20
 * Time: 00:29
 */

namespace Smtpd;

use Goetas\Mail\ToSwiftMailParser\MimeParser;
use Illuminate\Support\Facades\Log;
use Swift_Mime_SimpleMimeEntity as MessagePart;
use Zend\Mail\Message as ZendMessage;

class MessageFactory
{
    /**
     * Create a new message instance from the given zend message
     *
     * @param ZendMessage $zendMessage
     * @param string             $from
     * @param array              $recipients
     *
     * @return Message
     */
    public static function make(ZendMessage $zendMessage, string $from, array $recipients)
    {
        return (new Message())
            ->setZendMessage($zendMessage)
            ->from($from)
            ->to($recipients);
    }
}
