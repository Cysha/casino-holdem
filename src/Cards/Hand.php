<?php

namespace Cysha\Casino\Holdem\Cards;

use Cysha\Casino\Holdem\Game\Player;

class Hand implements \Countable
{
    /**
     * @var CardCollection
     */
    private $cards;

    /**
     * @var Player
     */
    private $player;

    /**
     * Hand constructor.
     *
     * @param CardCollection $cards
     * @param Player         $player
     */
    private function __construct(CardCollection $cards, Player $player)
    {
        $this->cards = $cards;
        $this->player = $player;
    }

    /**
     * @var string
     * @var Player $player
     *
     * @return static
     */
    public static function createUsingString(string $cards, Player $player)
    {
        $cards = explode(' ', $cards);

        return static::create(CardCollection::make($cards)->map(function ($card) {
            return Card::fromString($card);
        }), $player);
    }

    /**
     * @var string
     * @var Player $player
     *
     * @return static
     */
    public static function create(CardCollection $cards, Player $player)
    {
        return new static($cards, $player);
    }

    /**
     * @return Player
     */
    public function player(): Player
    {
        return $this->player;
    }

    /**
     * @return CardCollection
     */
    public function cards(): CardCollection
    {
        return $this->cards;
    }

    /**
     * Count cards in the hand.
     */
    public function count(): int
    {
        return $this->cards()->count();
    }

    /**
     * @param Card $card
     */
    public function addCard(Card $card)
    {
        $this->cards = $this->cards()->push($card);
    }
}
