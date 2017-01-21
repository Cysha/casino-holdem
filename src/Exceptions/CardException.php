<?php

namespace xLink\Poker\Exceptions;

use Exception;

class CardException extends Exception
{
    /**
     * @param string $value
     *
     * @return static
     */
    public static function invalidCardString($value, $message = null)
    {
        $defaultMessage = sprintf('Cannot create card from string given: "%s"', $value);
        $message = is_null($message) ? $defaultMessage : $message;

        return new static($message);
    }

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

    /**
     * @param int    $count
     * @param string $message
     *
     * @return static
     */
    public static function noCardValueGroupsFound(int $count,  $message = null)
    {
        $defaultMessage = sprintf('Tried to find %d cards with the same value, but failed', $count);
        $message = is_null($message) ? $defaultMessage : $message;

        return new static($message);
    }
}
