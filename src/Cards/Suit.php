<?php

namespace xLink\Poker\Cards;

final class Suit
{
    const CLUB = 100;
    const DIAMOND = 101;
    const HEART = 102;
    const SPADE = 103;

    private static $suits = [];

    /**
     * @param int $suit
     */
    private function __construct(int $suit)
    {
        $this->suit = $suit;
    }

    /**
     * Make a Club suit.
     *
     * @return Suit
     */
    public static function club(): Suit
    {
        return static::makeSuit(static::CLUB);
    }

    /**
     * Make a Diamnod suit.
     *
     * @return Suit
     */
    public static function diamond(): Suit
    {
        return static::makeSuit(static::DIAMOND);
    }

    /**
     * Make a Heart suit.
     *
     * @return Suit
     */
    public static function heart(): Suit
    {
        return static::makeSuit(static::HEART);
    }

    /**
     * Make a Spade suit.
     *
     * @return Suit
     */
    public static function spade(): Suit
    {
        return static::makeSuit(static::SPADE);
    }

    /**
     * @param int
     *
     * @return Suit
     */
    private static function makeSuit(int $suit): Suit
    {
        if (!array_key_exists($suit, static::$suits)) {
            static::$suits[$suit] = new static($suit);
        }

        return static::$suits[$suit];
    }

    /**
     * Get the suit unique Id.
     *
     * @return int
     */
    public function value()
    {
        return $this->suit;
    }

    /**
     * Get the suit name.
     *
     * @return string
     */
    public function name()
    {
        switch ($this->suit) {
            case static::CLUB:
                $suit = 'club';
            break;
            case static::DIAMOND:
                $suit = 'diamond';
            break;
            case static::HEART:
                $suit = 'heart';
            break;
            case static::SPADE:
                $suit = 'spade';
            break;
        }

        return $suit;
    }

    /**
     * Get the suit symbol.
     *
     * @return string
     */
    public function symbol()
    {
        switch ($this->suit) {
            case static::CLUB:
                $symbol = '♣';
            break;
            case static::DIAMOND:
                $symbol = '♦';
            break;
            case static::HEART:
                $symbol = '♥';
            break;
            case static::SPADE:
                $symbol = '♠';
            break;
        }

        return $symbol;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->symbol();
    }

    /**
     * @param Suit $suit
     *
     * @return bool
     */
    public function equals($suit)
    {
        return get_class($suit) === static::class
            && $suit->suit === $this->suit;
    }
}
