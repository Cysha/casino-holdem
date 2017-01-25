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
        $cards = $board->merge($hand);

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

    /**
     * @param CardCollection $cards
     *
     * @return bool|CardCollection
     */
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
