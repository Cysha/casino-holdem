<?php

namespace xLink\Poker\Cards\Evaluators;

use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Contracts\CardEvaluator;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;

class SevenCard implements CardEvaluator
{
    public static function evaluate(CardCollection $board, Hand $hand)
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
    }

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

        return $royalFlushHand;
    }

    public static function straightFlush(CardCollection $cards)
    {
        // check for flush
        if (!static::flush($cards)) {
            return false;
        }

        // check for straight
        if (!static::straight($cards)) {
            return false;
        }

        return true;
    }

    // public static function fourOfAKind(CardCollection $cards) {}

    // public static function fullHouse(CardCollection $cards) {}

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

        return CardCollection::make($groupedBySuit->first());
    }

    public static function straight(CardCollection $cards)
    {
        $cards = $cards
            ->sortBy(function (Card $card) {
                return $card->value();
            }, SORT_NUMERIC)
            ->values();

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
            return $cards->splice($highestSequentialPosition - 4, 5);
        }

        return false;
    }

    // public static function threeOfAKind(CardCollection $cards) {}

    // public static function twoPair(CardCollection $cards) {}

    // public static function pair(CardCollection $cards) {}

    // public static function highCard(CardCollection $cards) {}
}
