<?php

namespace xLink\Poker\Game;

use xLink\Poker\Cards\Contracts\CardEvaluator;
use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;

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
            ->map(function (Hand $hand) use ($board) {
                return SevenCard::evaluate($board, $hand);
            })
            ->sortByDesc(function (SevenCardResult $result) {
                return $result->rank();
            })
            ->groupBy(function (SevenCardResult $result) {
                return $result->rank();
            })
        ;

        return $playerHands->first();
    }
}
