<?php

namespace xLink\Poker\Cards\Evaluators;

use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Contracts\CardEvaluator;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;

class SevenCard implements CardEvaluator
{
    /*public static function evaluate(CardCollection $board, Hand $hand)
    {
        $cards = $board->merge($hand);

        if (static::royalFlush($cards)) {
            return 'Royal Flush';
        }

        if (static::flush($cards)) {
            return 'Flush';
        }

        if (static::straight($cards)) {
            return 'Straight';
        }

        return false;
    }*/

    public static function royalFlush(CardCollection $cards)
    {
        // check for straight flush
        if (!static::straightFlush($cards)) {
            return false;
        }

        // make sure that TJQKA exist in hand
        $royalFlushHand = $cards->filter(
            function (Card $card) {
                return $card->isFaceCard() || $card->value() === 10;
            }
        );

        if ($royalFlushHand->count() < 5) {
            return false;
        }

        return $royalFlushHand->sortByValue();
    }

    public static function straightFlush(CardCollection $cards)
    {
        // check for flush
        if (static::flush($cards) === false) {
            return false;
        }

        // check for straight
        if (($straight = static::straight($cards)) === false) {
            return false;
        }

        return $straight;
    }

    // public static function fourOfAKind(CardCollection $cards) {}

    // public static function fullHouse(CardCollection $cards) {}

    /**
     * @param CardCollection $cards
     *
     * @return CardCollection|bool
     */
    public static function flush(CardCollection $cards)
    {
        $groupedBySuit = $cards
            ->groupBy(function (Card $card) {
                return $card->suit()->name();
            })->sort(function ($group) {
                return count($group);
            });

        if ($groupedBySuit->first()->count() < 5) {
            return false;
        }

        return $groupedBySuit->first()->sortByValue();
    }

    /**
     * @param CardCollection $cardCollection
     *
     * @return bool|static
     */
    public static function straight(CardCollection $cardCollection)
    {
        $check = static::checkForHighLowStraight($cardCollection->sortByValue()->unique());
        if ($check !== false) {
            return $check;
        }

        // check for straight with the current card list
        $check = static::checkForStraight($cardCollection->sortByValue()->unique());
        if ($check !== false) {
            return $check;
        }

        return false;
    }

    // public static function threeOfAKind(CardCollection $cards) {}

    // public static function twoPair(CardCollection $cards) {}

    // public static function pair(CardCollection $cards) {}

    // public static function highCard(CardCollection $cards) {}

    private static function checkForHighLowStraight(CardCollection $cards)
    {
        // check for aces before we write off the straight possibility
        if (($aceCount = $cards->whereValue(Card::ACE)->count()) === 0) {
            return false;
        }

        // check for A2345 via card values
        $lowAStraight = $cards->only(range(0, 4));
        $lowSum = $lowAStraight
            ->sum(function (Card $card) {
                return $card->value();
            });
        if ($lowSum === 15) {
            return $lowAStraight;
        }

        // check for TJQKA via card values
        $highAStraight = $cards
            ->switchAceValue()
            ->sortByValue()
            ->only(range(6, 2))
            ->values();

        $highSum = $highAStraight->sum(function (Card $card) {
            return $card->value();
        });
        if ($highSum === 60) {
            return $highAStraight;
        }

        return false;
    }

    private static function checkForStraight(CardCollection $cards)
    {
        $runningLength = 0;
        $highestSequentialPosition = 0;

        $cardCount = $cards->count() - 1;
        for ($i = 0; $i < $cardCount; ++$i) {
            $nextCard = $cards->get($i + 1)->value();
            $thisCard = $cards->get($i)->value();

            $cardsAreNotSequential = $nextCard - $thisCard !== 1;
            if ($cardsAreNotSequential) {
                $runningLength = 0;
            }

            if ($cardsAreNotSequential === false) {
                $highestSequentialPosition = $i + 1;
                ++$runningLength;
            }
        }

        if ($runningLength === 4) {
            return $cards
                ->only(range($highestSequentialPosition - 4, $highestSequentialPosition))
                ->values();
        }

        return false;
    }
}
