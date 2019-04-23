<?php
/**
 * Created by PhpStorm.
 * User: aaronschmied
 * Date: 2019-04-20
 * Time: 00:29
 */

namespace Smtpd;

use Illuminate\Support\Facades\Storage;
use Zend\Mail\Message as ZendMessage;
use Zend\Mime\Message as ZendMimeMessage;

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

        $message->subject($zendMessage->getSubject());

        $message->from($from);

        $message->to($recipients);

        $mimeMessage = ZendMimeMessage::createFromMessage($zendMessage->toString());

        foreach ($mimeMessage->getParts() as $part) {
            switch ($part->getType()) {
                case 'text/html':
                    $message->html($part->getContent());
                    break;
                case 'text/plain':
                    $message->text($part->getContent());
                    break;
                default:
                    $message->attachData($part->getContent(), $part->getFileName());
            }
        }

        $message->setZendMessage($zendMessage);

        $filename = uniqid().".eml";
        Storage::disk('local')->put($filename, $zendMessage->toString());
        dd($filename);
        return $message;
    }
}
