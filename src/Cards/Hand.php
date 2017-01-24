<?php

namespace xLink\Poker\Cards;

use xLink\Poker\Game\Player;

class Hand extends CardCollection
{
    /**
     * @var string
     * @var Player $player
     *
     * @return static
     */
    public static function createUsingString(string $cards, Player $player)
    {
        $cards = explode(' ', $cards);

        return static::make($cards)->map(function ($card) {
            return Card::fromString($card);
        });
    }

    /**
     * @var string
     * @var Player $player
     *
     * @return static
     */
    public static function create(array $cards, Player $player)
    {
        return static::make($cards);
    }
}
