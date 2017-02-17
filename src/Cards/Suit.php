<?php

namespace Cysha\Casino\Holdem\Cards;

use Cysha\Casino\Holdem\Exceptions\CardException;

final class Suit
{
    const CLUB = 100;
    const DIAMOND = 101;
    const HEART = 102;
    const SPADE = 103;

    const CLUB_SYMBOL = "\u{2663}";
    const DIAMOND_SYMBOL = "\u{2666}";
    const HEART_SYMBOL = "\u{2665}";
    const SPADE_SYMBOL = "\u{2660}";

    const CLUB_LETTER = 'c';
    const DIAMOND_LETTER = 'd';
    const HEART_LETTER = 'h';
    const SPADE_LETTER = 's';

    private static $symbols = [
        self::CLUB => self::CLUB_SYMBOL,
        self::DIAMOND => self::DIAMOND_SYMBOL,
        self::HEART => self::HEART_SYMBOL,
        self::SPADE => self::SPADE_SYMBOL,
    ];

    private static $letters = [
        self::CLUB => self::CLUB_LETTER,
        self::DIAMOND => self::DIAMOND_LETTER,
        self::HEART => self::HEART_LETTER,
        self::SPADE => self::SPADE_LETTER,
    ];

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
        return new static($suit);
    }

    /**
     * @param $stringValue
     *
     * @return Suit
     */
    private static function getValueFromLetter($stringValue): Suit
    {
        $values = array_values(static::$letters);
        $key = array_search($stringValue, $values, true);

        return static::makeSuit($key + 100);
    }

    /**
     * @param $stringValue
     *
     * @return Suit
     */
    private static function getValueFromSymbol($stringValue): Suit
    {
        $values = array_values(static::$symbols);
        $key = array_search($stringValue, $values, true);

        return static::makeSuit($key + 100);
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
            default:
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
                $symbol = self::CLUB_SYMBOL;
            break;
            case static::DIAMOND:
                $symbol = self::DIAMOND_SYMBOL;
            break;
            case static::HEART:
                $symbol = self::HEART_SYMBOL;
            break;
            default:
            case static::SPADE:
                $symbol = self::SPADE_SYMBOL;
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

    /**
     * @param $stringValue
     *
     * @return Suit
     *
     * @throws CardException
     */
    public static function fromString($stringValue)
    {
        $stringValue = strtolower($stringValue);

        if (in_array($stringValue, static::$letters, true)) {
            return static::getValueFromLetter($stringValue);
        }

        if (in_array($stringValue, static::$symbols, true)) {
            return static::getValueFromSymbol($stringValue);
        }

        throw CardException::unexpectedSuit($stringValue);
    }
}
