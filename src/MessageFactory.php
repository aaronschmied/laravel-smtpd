<?php
/**
 * Created by PhpStorm.
 * User: aaronschmied
 * Date: 2019-04-20
 * Time: 00:29
 */

namespace Smtpd;

class MessageFactory
{
    /**
     * Create a new message instance from the given zend message
     *
     * @param string $content
     * @param string $from
     * @param array  $to
     *
     * @return Message
     */
    public static function make(string $content, string $from, array $to)
    {
        return new Message($content, $from, $to);
    }
}
