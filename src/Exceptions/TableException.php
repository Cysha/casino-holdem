<?php

namespace Cysha\Casino\Holdem\Exceptions;

use Cysha\Casino\Game\Client;
use Cysha\Casino\Holdem\Game\Table;
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

    /**
     * @param Client $player
     * @param Table $table
     * @param string $message
     *
     * @return static
     */
    public static function notRegistered(Client $player, Table $table, $message = null)
    {
        $defaultMessage = sprintf('%s is not registered to table: "%s"', $player, $table->id()->toString());
        $message = is_null($message) ? $defaultMessage : $message;

        return new static($message);
    }

}
