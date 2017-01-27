<?php

namespace xLink\Poker\Exceptions;

use DomainException;
use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Player;

class RoundException extends DomainException
{
    /**
     * @param Client      $player
     * @param null|string $message
     *
     * @return static
     */
    public static function playerHasNoHand(Client $player, $message = null)
    {
        $defaultMessage = sprintf('%s has not been dealt into this round', $player);
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param Player      $player
     * @param Chips       $chips
     * @param null|string $message
     *
     * @return static
     */
    public static function notEnoughChipsInChipStack(Player $player, Chips $chips, $message = null)
    {
        $defaultMessage = sprintf('%s does not have enough chips to bet %d', $player, $chips->amount());
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param Player      $player
     * @param null|string $message
     *
     * @return static
     */
    public static function playerHasNoActiveHand(Player $player, $message = null)
    {
        $defaultMessage = sprintf('%s does not have an active hand', $player);
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }
}
