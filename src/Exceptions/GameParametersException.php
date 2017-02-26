<?php

namespace Cysha\Casino\Holdem\Exceptions;

use Exception;

class GameParametersException extends Exception
{
    /**
     * @param null $message
     *
     * @return static
     */
    public static function invalidArgument($message = null)
    {
        $defaultMessage = sprintf('Invalid argument passed in GameParameters.');
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }
}
