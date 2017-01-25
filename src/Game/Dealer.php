<?php

namespace xLink\Poker\Game;

use Illuminate\Support\Collection;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Contracts\CardEvaluator;
use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Results\SevenCardResult;

class Dealer
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var CardEvaluator
     */
    private $cardEvaluationRules;

    /**
     * Dealer constructor.
     *
     * @param Deck          $deck
     * @param CardEvaluator $cardEvaluationRules
     */
    private function __construct(Deck $deck, CardEvaluator $cardEvaluationRules)
    {
        $this->deck = $deck;
        $this->cardEvaluationRules = $cardEvaluationRules;
    }

    /**
     * @param Deck          $deck
     * @param CardEvaluator $cardEvaluationRules
     *
     * @return Dealer
     */
    public static function startWork(Deck $deck, CardEvaluator $cardEvaluationRules): Dealer
    {
        return new self($deck, $cardEvaluationRules);
    }

    /**
     * @param CardCollection $board
     * @param Hand[]         $playerHands
     *
     * @return Hand
     */
    public static function evaluateHands(CardCollection $board, Hand ...$playerHands)
    {
        $playerHands = collect($playerHands)
            // evaluate hands
            ->map(function (Hand $hand) use ($board) {
                return SevenCard::evaluate($board, $hand);
            })

            // sort the hands by their hand rank
            ->sortByDesc(function (SevenCardResult $result) {
                return [$result->rank(), $result->value()];
            })

            // group by the hand rank
            ->groupBy(function (SevenCardResult $result) {
                return $result->rank();
            })

            // sort the collection by the count
            ->sortByDesc(function (Collection $collection) {
                return $collection->count();
            })
        ;

        // if all hands in the first collection are equal
        if ($playerHands->first()->count() === 1) {
            return $playerHands->first();
        }

        // sort hands in first collection by hand value and return it
        return $playerHands->first()
            ->flatten()
            ->sortByDesc(function (SevenCardResult $result) {
                return $result->value();
            })
            ->groupBy(function (SevenCardResult $result) {
                return $result->value();
            })
            ->first();
    }
}
