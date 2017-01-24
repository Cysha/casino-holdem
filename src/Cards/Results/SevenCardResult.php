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

    /** @var int */
    private $handRank = 0;

    /** @var CardCollection */
    private $cards;

    /** @var string */
    private $definition = null;

    private function __construct($rank, CardCollection $cards, string $definition)
    {
        $this->handRank = $rank;
        $this->cards = $cards;
        $this->definition = $definition;
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
     * @return string
     */
    public function definition(): string
    {
        return $this->definition;
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createRoyalFlush(CardCollection $cards): self
    {
        return new static(self::ROYAL_FLUSH, $cards, 'Royal Flush');
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createStraightFlush(CardCollection $cards): self
    {
        $highCard = $cards->sortByValue()->take(-1);
        $definition = sprintf('Straight Flush to %s', $highCard->first()->name());

        return new static(self::STRAIGHT_FLUSH, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFourOfAKind(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(1);
        $definition = sprintf('4 of a Kind - %ss', $cardGroups->first()->first()->name());

        return new static(self::FOUR_OF_A_KIND, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFullHouse(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(2);
        $definition = sprintf(
            'Full House - %ss over %ss',
            $cardGroups->first()->first()->name(),
            $cardGroups->last()->first()->name()
        );

        return new static(self::FULL_HOUSE, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFlush(CardCollection $cards): self
    {
        $highCard = $cards->sortByValue()->take(-1);
        $definition = sprintf('Flush to %s', $highCard->first()->name());

        return new static(self::FLUSH, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createStraight(CardCollection $cards): self
    {
        $highCard = $cards->sortByValue()->take(-1);
        $definition = sprintf('Straight to %s', $highCard->first()->name());

        return new static(self::STRAIGHT, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createThreeOfAKind(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(1);
        $definition = sprintf('3 of a Kind - %ss', $cardGroups->first()->first()->name());

        return new static(self::THREE_OF_A_KIND, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createTwoPair(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(2);

        $definition = sprintf(
            'Two Pair - %ss and %ss',
            $cardGroups->first()->first()->name(),
            $cardGroups->last()->first()->name()
        );

        return new static(self::TWO_PAIR, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createOnePair(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(1);
        $definition = sprintf('Pair of %ss', $cardGroups->first()->first()->name());

        return new static(self::ONE_PAIR, $cards, $definition);
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createHighCard(CardCollection $cards): self
    {
        $highCard = $cards->sortByValue()->take(-1);
        $definition = sprintf('High Card - %s', $highCard->first()->name());

        return new static(self::HIGH_CARD, $cards, $definition);
    }
}
