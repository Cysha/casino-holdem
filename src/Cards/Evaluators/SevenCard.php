<?php

namespace Cysha\Casino\Holdem\Cards\Evaluators;

use Cysha\Casino\Cards\Card;
use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Contracts\CardEvaluator;
use Cysha\Casino\Cards\Contracts\CardResults;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Cards\HandCollection;
use Cysha\Casino\Cards\ResultCollection;
use Cysha\Casino\Holdem\Cards\Results\SevenCardResult;
use Cysha\Casino\Holdem\Cards\SevenCardResultCollection;
use Illuminate\Support\Collection;

class SevenCard implements CardEvaluator
{
    /**
     * @param CardCollection $board
     * @param Hand           $hand
     *
     * @return CardResults
     */
    public static function evaluate(CardCollection $board, Hand $hand): CardResults
    {
        $cards = $board->merge($hand->cards());

        if (($result = static::royalFlush($cards)) !== false) {
            return SevenCardResult::createRoyalFlush($result, $hand);
        }

        if (($result = static::straightFlush($cards)) !== false) {
            return SevenCardResult::createStraightFlush($result, $hand);
        }

        if (($result = static::fourOfAKind($cards)) !== false) {
            return SevenCardResult::createFourOfAKind($result, $hand);
        }

        if (($result = static::fullHouse($cards)) !== false) {
            return SevenCardResult::createFullHouse($result, $hand);
        }

        if (($result = static::flush($cards)) !== false) {
            return SevenCardResult::createFlush($result, $hand);
        }

        if (($result = static::straight($cards)) !== false) {
            return SevenCardResult::createStraight($result, $hand);
        }

        if (($result = static::threeOfAKind($cards)) !== false) {
            return SevenCardResult::createThreeOfAKind($result, $hand);
        }

        if (($result = static::twoPair($cards)) !== false) {
            return SevenCardResult::createTwoPair($result, $hand);
        }

        if (($result = static::onePair($cards)) !== false) {
            return SevenCardResult::createOnePair($result, $hand);
        }

        return SevenCardResult::createHighCard(static::highCard($cards), $hand);
    }

    /**
     * @param CardCollection $board
     * @param HandCollection $playerHands
     *
     * @return ResultCollection
     */
    public function evaluateHands(CardCollection $board, HandCollection $playerHands): ResultCollection
    {
        $playerHands = $playerHands
        // evaluate hands
        ->map(function (Hand $hand) use ($board) {
            return static::evaluate($board, $hand);
        })

        // sort the hands by their hand rank
            ->sortByDesc(function (SevenCardResult $result) {
                return [$result->rank(), $result->value()];
            })

        // group by the hand rank
            ->groupBy(function (SevenCardResult $result) {
                return $result->rank();
            })
        ;

        // if all hands in the first collection are equal
        $handsAreEqual = $playerHands
            ->first()
            ->groupBy(function (SevenCardResult $result) {
                return array_sum($result->value());
            })
        ;

        $winningResults = SevenCardResultCollection::make($handsAreEqual->first()->toArray());

        return $winningResults;
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
        $royalFlushHand = $cards
            ->switchAceValue()
            ->filter(function (Card $card) {
                return $card->isFaceCard() || $card->value() === 10;
            });

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

        // we only care about the first instance of a card number
        $cardCollection = $cardCollection->uniqueByValue();

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

        $uniqueCards = $cards->map->value()->unique();
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
