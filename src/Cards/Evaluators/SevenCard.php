<?php

namespace xLink\Poker\Cards\Evaluators;

use xLink\Poker\Cards\Contracts\CardEvaluator;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Hand;

class SevenCard implements CardEvaluator
{
    public static function evaluate(CardCollection $board, Hand $hand)
    {
        $cards = $board->merge($hand->cards());

        if (($result = static::royalFlush($cards)) !== false) {
            return SevenCardResult::createRoyalFlush($result);
        }

        if (($result = static::straightFlush($cards)) !== false) {
            return SevenCardResult::createStraightFlush($result);
        }

        if (($result = static::fourOfAKind($cards)) !== false) {
            return SevenCardResult::createFourOfAKind($result);
        }

        if (($result = static::fullHouse($cards)) !== false) {
            return SevenCardResult::createFullHouse($result);
        }

        if (($result = static::flush($cards)) !== false) {
            return SevenCardResult::createFlush($result);
        }

        if (($result = static::straight($cards)) !== false) {
            return SevenCardResult::createStraight($result);
        }

        if (($result = static::threeOfAKind($cards)) !== false) {
            return SevenCardResult::createThreeOfAKind($result);
        }

        if (($result = static::twoPair($cards)) !== false) {
            return SevenCardResult::createTwoPair($result);
        }

        if (($result = static::onePair($cards)) !== false) {
            return SevenCardResult::createOnePair($result);
        }

        return SevenCardResult::createHighCard(static::highCard($cards));
    }

    /**
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
    public static function royalFlush(CardCollection $cards)
    {
        // check for straight flush
        if (!static::straightFlush($cards)) {
            return false;
        }

        // make sure that TJQKA exist in hand
        $royalFlushHand = $cards->switchAceValue()->filter(
            function (Card $card) {
                return $card->isFaceCard() || $card->value() === 10;
            }
        );

        if ($royalFlushHand->count() < 5) {
            return false;
        }

        return $royalFlushHand->sortByValue();
    }

    /**
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
    public static function straightFlush(CardCollection $cards)
    {
        // check for flush
        if (($flushCards = static::flush($cards)) === false) {
            return false;
        }

        // check for straight, using the flush cards
        if (($straight = static::straight($flushCards)) === false) {
            return false;
        }

        return $straight;
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|false
     */
    public static function fourOfAKind(CardCollection $cards)
    {
        $judgedHand = self::nNumberOfCardsInSet($cards, 4);

        if ($judgedHand === null) {
            return false;
        }

        $highCard = self::highCard($cards->diff($judgedHand))->last();

        return $judgedHand
            ->push($highCard)
            ->switchAceValue()
            ->sortByValue();
    }

    /**
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
    public static function fullHouse(CardCollection $cards)
    {
        $threeOfAKind = self::nNumberOfCardsInSet($cards, 3);
        $twoOfAKind = self::nNumberOfCardsInSet($cards->diff($threeOfAKind), 2);

        if ($threeOfAKind === null || $twoOfAKind === null) {
            return false;
        }

        return $threeOfAKind->merge($twoOfAKind);
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|bool
     */
    public static function flush(CardCollection $cards)
    {
        $groupedBySuit = $cards
            ->switchAceValue()
            ->groupBy(function (Card $card) {
                return $card->suit()->name();
            })
            ->sortByDesc(function ($group) {
                return count($group);
            });
        if ($groupedBySuit->first()->count() < 5) {
            return false;
        }

        return $groupedBySuit
            ->first()
            ->sortByValue()
            ->take(-5)
            ->values();
    }

    /**
     * @param CardCollection $cardCollection
     *
     * @return bool|CardCollection
     */
    public static function straight(CardCollection $cardCollection)
    {
        // a straight has to have a 5 or 10 in
        if ($cardCollection->whereValue(5)->count() === 0 && $cardCollection->whereValue(10)->count() === 0) {
            return false;
        }

        // check with ace == 1
        $check = static::checkForStraight($cardCollection->sortByValue()->unique());
        if ($check !== false) {
            return $check;
        }

        // check with ace == 14
        $check = static::checkForStraight($cardCollection->switchAceValue()->sortByValue()->unique());
        if ($check !== false) {
            return $check;
        }

        return false;
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|bool
     */
    public static function threeOfAKind(CardCollection $cards)
    {
        $judgedHand = self::nNumberOfCardsInSet($cards, 3);

        if ($judgedHand === null) {
            return false;
        }

        $highCards = $cards->diff($judgedHand)->sortByValue()->reverse()->take(2);

        return $judgedHand
            ->merge($highCards)
            ->switchAceValue()
            ->sortByValue();
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|bool
     */
    public static function twoPair(CardCollection $cards)
    {
        $pairOne = self::nNumberOfCardsInSet($cards, 2);
        $pairTwo = self::nNumberOfCardsInSet($cards->diff($pairOne), 2);

        if ($pairTwo === null) {
            return false;
        }

        $pairs = $pairOne->merge($pairTwo);

        $highCard = self::highCard($cards->diff($pairs))->last();

        return $pairs
            ->push($highCard)
            ->switchAceValue();
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|false
     */
    public static function onePair(CardCollection $cards)
    {
        $cards = $cards->switchAceValue();
        $pair = self::nNumberOfCardsInSet($cards, 2);

        if ($pair === null) {
            return false;
        }

        $otherCardsInResult = $cards->diff($pair)
            ->sortByValue()
            ->reverse()
            ->take(3)
        ;

        return $pair->merge($otherCardsInResult);
    }

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection
     */
    public static function highCard(CardCollection $cards): CardCollection
    {
        return $cards
            ->switchAceValue()
            ->sortByValue()
            ->reverse()
            ->take(5)
            ->reverse()
            ->values();
    }

    /**
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
    private static function checkForStraight(CardCollection $cards)
    {
        // check 2-6
        $cardsToCheck = static::isStraight($cards->only(range(2, 6)));
        if ($cardsToCheck !== false) {
            return $cardsToCheck;
        }

        // check 1-5
        $cardsToCheck = static::isStraight($cards->only(range(1, 5)));
        if ($cardsToCheck !== false) {
            return $cardsToCheck;
        }

        // check 0-4
        $cardsToCheck = static::isStraight($cards->only(range(0, 4)));
        if ($cardsToCheck !== false) {
            return $cardsToCheck;
        }

        return false;
    }

    /**
     * @author Derecho
     *
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
    private static function isStraight(CardCollection $cards)
    {
        if ($cards->count() !== 5) {
            return false;
        }

        $uniqueCards = $cards->map(function (Card $card) {
            return $card->value();
        })->unique();
        if ($cards->count() !== $uniqueCards->count()) {
            return false;
        }

        if ($cards->sumByValue() === array_sum(range(
            $cards->sortByValue()->first()->value(),
            $cards->sortByValue()->last()->value()
        ))) {
            return $cards->sortByValue()->values();
        }

        return false;
    }

    /**
     * @param CardCollection $cards
     * @param int            $numberOfCardsOfType
     *
     * @return CardCollection
     */
    private static function nNumberOfCardsInSet(CardCollection $cards, int $numberOfCardsOfType)
    {
        $judgedHand = $cards
            ->groupBy(function (Card $card) {
                return $card->value();
            })
            ->filter(function (CardCollection $group) use ($numberOfCardsOfType) {
                return $group->count() === $numberOfCardsOfType;
            })
            ->sortBy(function (CardCollection $group) {
                return $group->count();
            })
            ->values()
            ->last()
        ;

        return $judgedHand;
    }
}
