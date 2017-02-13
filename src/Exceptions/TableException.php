<?php

namespace xLink\Poker\Exceptions;

use Exception;

class TableException extends Exception
{
    /**
     * @param null $message
     *
     * @return static
     */
    public static function invalidButtonPosition($message = null)
    {
        $defaultMessage = sprintf('Tried giving the button to a player that is not sat down.');
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }
}
