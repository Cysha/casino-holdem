<?php

namespace xLink\Poker\Cards\Results;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Contracts\CardResults;

class SevenCardResult implements CardResults
{
    const ROYAL_FLUSH = 9;
    const STRAIGHT_FLUSH = 8;
    const FOUR_OF_A_KIND = 7;
    const FULL_HOUSE = 6;
    const FLUSH = 5;
    const STRAIGHT = 4;
    const THREE_OF_A_KIND = 3;
    const TWO_PAIR = 2;
    const ONE_PAIR = 1;
    const HIGH_CARD = 0;

    private $handRank = 0;
    private $cards;

    private function __construct($rank, CardCollection $cards)
    {
        $this->handRank = $rank;
        $this->cards = $cards;
    }

    /**
     * @return int
     */
    public function rank(): int
    {
        return $this->handRank;
    }

    /**
     * @return CardCollection
     */
    public function cards(): CardCollection
    {
        return $this->cards;
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createRoyalFlush(CardCollection $cards): self
    {
        return new static(self::ROYAL_FLUSH, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createStraightFlush(CardCollection $cards): self
    {
        return new static(self::STRAIGHT_FLUSH, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFourOfAKind(CardCollection $cards): self
    {
        return new static(self::FOUR_OF_A_KIND, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFullHouse(CardCollection $cards): self
    {
        return new static(self::FULL_HOUSE, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFlush(CardCollection $cards): self
    {
        return new static(self::FLUSH, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createStraight(CardCollection $cards): self
    {
        return new static(self::STRAIGHT, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createThreeOfAKind(CardCollection $cards): self
    {
        return new static(self::THREE_OF_A_KIND, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createTwoPair(CardCollection $cards): self
    {
        return new static(self::TWO_PAIR, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createOnePair(CardCollection $cards): self
    {
        return new static(self::ONE_PAIR, $cards);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createHighCard(CardCollection $cards): self
    {
        return new static(self::HIGH_CARD, $cards);
    }
}
