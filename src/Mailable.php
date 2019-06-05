<?php

namespace Smtpd;

use Goetas\Mail\ToSwiftMailParser\MimeParser;
use Illuminate\Mail\Mailable as IlluminateMailable;
use Swift_Message as SwiftMessage;
use Swift_Mime_SimpleMimeEntity as MessagePart;
use Zend\Mail\Message as ZendMessage;

class Mailable extends IlluminateMailable
{
    /**
     * @var ZendMessage?
     */
    protected $zendMessage;

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
     * Get the zend message.
     *
     * @return ZendMessage|null
     */
    public function getZendMessage(): ?ZendMessage
    {
        return $this->zendMessage;
    }

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

        $this->parseAddresses($zendMessage);
        $this->parseSubject($zendMessage);
        $this->parseHeaders($zendMessage);
        $this->parseContent($zendMessage);
    }

    /**
     * Parse the addresses from the zend message.
     *
     * @param ZendMessage $message
     */
    private function parseAddresses(ZendMessage $message)
    {
        foreach ($message->getFrom() as $from) {
            $this->from($from->getEmail(), $from->getName());
        }
        foreach ($message->getTo() as $to) {
            $this->to($to->getEmail(), $to->getName());
        }
        foreach ($message->getCc() as $cc) {
            $this->cc($cc->getEmail(), $cc->getName());
        }
        foreach ($message->getBcc() as $bcc) {
            $this->bcc($bcc->getEmail(), $bcc->getName());
        }
        foreach ($message->getReplyTo() as $replyTo) {
            $this->replyTo($replyTo->getEmail(), $replyTo->getName());
        }
    }

    /**
     * Parse the message subject.
     *
     * @param ZendMessage $message
     */
    private function parseSubject(ZendMessage $message)
    {
        $this->subject($message->getSubject());
    }

    /**
     * Parse the headers.
     *
     * @param ZendMessage $message
     */
    private function parseHeaders(ZendMessage $message)
    {
        $headers = $message->getHeaders()->toArray();
        $this->withSwiftMessage(function (SwiftMessage $message) use ($headers) {
            foreach ($headers as $name => $value) {
                if (is_array($value)) {
                    $value = implode("\n", $value);
                }
                $message->getHeaders()->addTextHeader($name, $value);
            }
        });
    }

    /**
     * Parse the content of the message.
     *
     * @param ZendMessage $message
     */
    private function parseContent(ZendMessage $message)
    {
        foreach ($this->parseMimeMessage($message->toString()) as $part) {
            if ($part->getContentType() == 'text/html') {
                $this->html($part->getBody());
            } else if ($part->getContentType() == 'text/plain') {
                $this->text($part->getBody());
            } else {
                $this->attachMimeEntity($part);
            }
        }

        // Set the fallback view
        if (!$this->html) {
            $this->html($message->getBodyText());
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
     * Attach a swift mime entity
     *
     * @param MessagePart $part
     *
     * @return $this
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


