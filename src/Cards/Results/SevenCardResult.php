<?php

namespace xLink\Poker\Cards\Results;

use xLink\Poker\Cards\Card;
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
    private $rank = 0;

    /** @var array */
    private $value = [];

    /** @var CardCollection */
    private $cards;

    /** @var string */
    private $definition = null;

    private function __construct(int $rank, array $value, CardCollection $cards, string $definition)
    {
        $this->rank = $rank;
        $this->value = $value;
        $this->cards = $cards;
        $this->definition = $definition;
    }

    /**
     * @return int
     */
    public function rank(): int
    {
        return $this->rank;
    }

    /**
     * @return array
     */
    public function value(): array
    {
        return $this->value;
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
        return new static(
            self::ROYAL_FLUSH,
            [0],
            $cards,
            'Royal Flush'
        );
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

        return new static(
            self::STRAIGHT_FLUSH,
            [$highCard->first()->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFourOfAKind(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue();

        $firstGroup = $cardGroups->first()->first();
        $kickerGroup = $cardGroups->last()->first();

        $definition = sprintf('4 of a Kind - %ss', $firstGroup->name());

        return new static(
            self::FOUR_OF_A_KIND,
            [$firstGroup->value(), $kickerGroup->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createFullHouse(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue()->take(2);

        $firstCardGroup = $cardGroups->first()->first();
        $lastCardGroup = $cardGroups->last()->first();
        $definition = sprintf(
            'Full House - %ss over %ss',
            $firstCardGroup->name(),
            $lastCardGroup->name()
        );

        return new static(
            self::FULL_HOUSE,
            [$firstCardGroup->value(), $lastCardGroup->value()],
            $cards,
            $definition
        );
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

        return new static(
            self::FLUSH,
            [$highCard->first()->value()],
            $cards,
            $definition
        );
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

        return new static(
            self::STRAIGHT,
            [$highCard->first()->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createThreeOfAKind(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue();

        $firstCard = $cardGroups->get(0)->first();
        $kicker = $cardGroups->get(1)->first();
        $definition = sprintf('3 of a Kind - %ss', $firstCard->name());

        return new static(
            self::THREE_OF_A_KIND,
            [$firstCard->value(), $kicker->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createTwoPair(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue();
        $firstCard = $cardGroups->get(0)->first();
        $secondCard = $cardGroups->get(1)->first();
        $kickerCard = $cardGroups->get(2)->first();

        $definition = sprintf(
            'Two Pair - %ss and %ss',
            $firstCard->name(),
            $secondCard->name()
        );

        return new static(
            self::TWO_PAIR,
            [$firstCard->value(), $secondCard->value(), $kickerCard->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createOnePair(CardCollection $cards): self
    {
        $cardGroups = $cards->groupByValue();

        $pair = $cardGroups->get(0)->first();
        $kicker = $cardGroups->get(1)->first();
        $definition = sprintf('Pair of %ss', $pair->name());

        return new static(
            self::ONE_PAIR,
            [$pair->value(), $kicker->value()],
            $cards,
            $definition
        );
    }

    /**
     * @param CardCollection $cards
     *
     * @return SevenCardResult
     */
    public static function createHighCard(CardCollection $cards): self
    {
        $cardValues = $cards->sortByValue()->values();

        $highCard = $cardValues->get(4);

        $definition = sprintf('High Card - %s', $highCard->name());

        return new static(
            self::HIGH_CARD,
            $cardValues->map(function (Card $card) {
                return $card->value();
            })->toArray(),
            $cards,
            $definition
        );
    }
//
//    /**
//     * @param SevenCardResult $object
//     *
//     * @return bool
//     */
//    public function equals($object)
//    {
//        return $this->rank() === $object->rank()
//            && $this->value() === $object->value()
//            && $this->definition() === $object->definition()
//            && $this->cards()->__toString() === $object->cards()->__toString()
//        ;
//    }
//
//    /**
//     * @return array
//     */
//    public function toArray(): array
//    {
//        return [
//            'rank' => $this->rank(),
//            'value' => $this->value(),
//            'definition' => $this->definition(),
//            'cards' => $this->cards()->__toString(),
//        ];
//    }
}
