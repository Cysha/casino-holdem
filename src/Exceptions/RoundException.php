<?php

namespace Cysha\Casino\Holdem\Exceptions;

use DomainException;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Player;

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
    public static function playerStillNeedsToAct(Player $player, $message = null)
    {
        $defaultMessage = sprintf('%s still needs to act.', $player);
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param null|string $message
     *
     * @return static
     */
    public static function flopHasBeenDealt($message = null)
    {
        $defaultMessage = 'The Flop has already been dealt!';
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param null|string $message
     *
     * @return static
     */
    public static function turnHasBeenDealt($message = null)
    {
        $defaultMessage = 'The Turn has already been dealt!';
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param null|string $message
     *
     * @return static
     */
    public static function riverHasBeenDealt($message = null)
    {
        $defaultMessage = 'The River has already been dealt!';
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

    /**
     * @param null|string $message
     *
     * @return static
     */
    public static function playerTryingToActOutOfTurn(Player $player, Player $actualPlayer, $message = null)
    {
        $defaultMessage = sprintf('%s tried to act out of turn! It\'s %ss turn.', $player, $actualPlayer);
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }

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
     * @param null $message
     *
     * @return static
     */
    public static function noPlayerActionsNeeded($message = null)
    {
        $defaultMessage = sprintf('A player is trying to act when there is no valid player actions left.');
        $message = null === $message ? $defaultMessage : $message;

        return new static($message);
    }
}
