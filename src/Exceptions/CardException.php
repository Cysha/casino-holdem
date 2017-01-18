<?php

namespace xLink\Poker\Exceptions;

use Exception;

class CardException extends Exception
{
    /**
     * @param string $message
     *
     * @return static
     */
    public static function unexpectedSuit($message = null)
    {
        $defaultMessage = 'Suit was not a reconigsed value, suit should be heart, club, diamond or spade';
        $message = is_null($message) ? $defaultMessage : $message;

        return new static($message);
    }
}
