<?php
/**
 * Copyright: © 2019 Pro Sales AG
 * Author: Aaron Schmied <aaron@pro-sales.ch>
 * Date: 2019-04-18
 * Time: 12:00
 */

namespace Smtpd;

use Goetas\Mail\ToSwiftMailParser\MimeParser;
use Illuminate\Mail\Mailable;
use Swift_Message as SwiftMessage;
use Swift_Mime_SimpleMimeEntity as MessagePart;
use Zend\Mail\Message as ZendMessage;

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

        $this->parseZendMessage();

        return $this;
    }

    /**
     * Parse the zend message
     *
     * @return void
     */
    protected function parseZendMessage()
    {
        $zendMessage = $this->getZendMessage();

        if (is_null($zendMessage)) {
            return;
        }

        $this->subject($zendMessage->getSubject());

        // Set the fallback view
        $this->html($zendMessage->getBodyText());


        foreach ($this->parseMimeMessage($zendMessage->toString()) as $part) {
            if ($part->getContentType() == 'text/html') {
                $this->html($part->getBody());
            } else if ($part->getContentType() == 'text/plain') {
                $this->text($part->getBody());
            } else {
                $this->attachMimeEntity($part);
            }
        }
    }

    /**
     * @param string $content
     *
     * @return MessagePart[]
     */
    private function parseMimeMessage(string $content): array
    {
        $mimeMessage = (new MimeParser())
            ->parseString($content);

        $parts = $this->getMimePartChildren($mimeMessage);

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
    private function getMimePartChildren(MessagePart $part): array
    {
        $children = [];
        foreach ($part->getChildren() as $child) {
            $children[] = $child;
            $children = array_merge($children, $this->getMimePartChildren($child));
        }
        return $children;
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

    /**
     * Attach a swift mime entity
     *
     * @param MessagePart $part
     *
     * @return Message
     */
    protected function attachMimeEntity(MessagePart $part)
    {
        return $this->withSwiftMessage(function (SwiftMessage $message) use ($part) {
            $message->attach($part);
        });
    }

    /**
     * Build the final message
     *
     * @return void
     */
    public function build()
    {
        return;
    }


}
