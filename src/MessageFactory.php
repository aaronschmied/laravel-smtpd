<?php
/**
 * Created by PhpStorm.
 * User: aaronschmied
 * Date: 2019-04-20
 * Time: 00:29
 */

namespace Smtpd;

use Goetas\Mail\ToSwiftMailParser\MimeParser;
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
        $message = new Message();

        $message
            ->subject($zendMessage->getSubject())
            ->setZendMessage($zendMessage)
            ->from($from)
            ->to($recipients);

        foreach (self::parseMessageParts($zendMessage->toString()) as $part) {
            if ($part->getContentType() == 'text/html') {
                $message->html($part->getBody());
            }
            else if ($part->getContentType() == 'text/plain') {
                $message->text($part->getBody());
            } else {
                $message->attachMimeEntity($part);
            }
        }
        return $message;
    }

    /**
     * @param string $content
     *
     * @return MessagePart[]
     */
    public static function parseMessageParts(string $content): array
    {
        $mail = (new MimeParser())
            ->parseString($content);

        $parts = static::getMessagePartChildren($mail);

        foreach ($parts as $index => $part) {
            if (strpos($part->getContentType(), 'multipart') === 0) {
                // Remove multipart types
                unset($parts[$index]);
            } else if (empty($part->getContentType())) {
                // Remove empty content
                unset($parts[$index]);
            }
        }

        return $parts;
    }

    /**
     * Get all children from a message part
     *
     * @param MessagePart $part
     *
     * @return MessagePart[]
     */
    private static function getMessagePartChildren(MessagePart $part): array
    {
        $children = [];
        foreach ($part->getChildren() as $child) {
            $children[] = $child;
            $children = array_merge($children, self::getMessagePartChildren($child));
        }
        return $children;
    }
}
