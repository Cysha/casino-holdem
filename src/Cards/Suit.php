<?php

namespace xLink\Poker\Cards;

use xLink\Poker\Exceptions\CardException;

final class Suit
{
    const CLUB = 100;
    const DIAMOND = 101;
    const HEART = 102;
    const SPADE = 103;

    private static $suits = [];

    private function __construct($suit)
    {
        $this->suit = $suit;
    }

    /**
     * Make a Club suit.
     *
     * @param bool $shareable share an instance of the suit
     *
     * @return Suit
     */
    public static function club($shareable = true)
    {
        if (!$shareable) {
            return new self(static::CLUB);
        }

        return static::makeSuit(static::CLUB);
    }

    /**
     * Make a Diamnod suit.
     *
     * @param bool $shareable share an instance of the suit
     *
     * @return Suit
     */
    public static function diamond($shareable = true)
    {
        if (!$shareable) {
            return new self(static::DIAMOND);
        }

        return static::makeSuit(static::DIAMOND);
    }

    /**
     * Make a Heart suit.
     *
     * @param bool $shareable share an instance of the suit
     *
     * @return Suit
     */
    public static function heart($shareable = true)
    {
        if (!$shareable) {
            return new self(static::HEART);
        }

        return static::makeSuit(static::HEART);
    }

    /**
     * Make a Spade suit.
     *
     * @param bool $shareable share an instance of the suit
     *
     * @return Suit
     */
    public static function spade($shareable = true)
    {
        if (!$shareable) {
            return new self(static::SPADE);
        }

        return static::makeSuit(static::SPADE);
    }

    private static function makeSuit($suit)
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
     *
     * @throws CardException
     */
    public function name()
    {
        switch ($this->suit) {
            case static::CLUB:
                return 'club';
            case static::DIAMOND:
                return 'diamond';
            case static::HEART:
                return 'heart';
            case static::SPADE:
                return 'spade';
        }

        throw CardException::unexpectedSuit();
    }

    /**
     * Get the suit symbol.
     *
     * @return string
     *
     * @throws CardException
     */
    public function symbol()
    {
        switch ($this->suit) {
            case static::CLUB:
                return '♣';
            case static::DIAMOND:
                return '♦';
            case static::HEART:
                return '♥';
            case static::SPADE:
                return '♠';
        }

        throw CardException::unexpectedSuit();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->symbol();
        } catch (CardException $e) {
            return 'Unknown suit';
        }
    }
}
