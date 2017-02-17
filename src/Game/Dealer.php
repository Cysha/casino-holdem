<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Holdem\Cards\Card;
use Cysha\Casino\Holdem\Cards\CardCollection;
use Cysha\Casino\Holdem\Cards\Contracts\CardEvaluator;
use Cysha\Casino\Holdem\Cards\Deck;
use Cysha\Casino\Holdem\Cards\SevenCardResultCollection;

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
     * @return Deck
     */
    public function deck(): Deck
    {
        return $this->deck;
    }

    /**
     * @return Card
     */
    public function dealCard(): Card
    {
        return $this->deck()->draw();
    }

    /**
     * Shuffles the deck.
     */
    public function shuffleDeck()
    {
        $this->deck()->shuffle();
    }

    /**
     * @param CardCollection $board
     * @param HandCollection $playerHands
     *
     * @return SevenCardResultCollection
     */
    public function evaluateHands(CardCollection $board, HandCollection $playerHands): SevenCardResultCollection
    {
        return $this->cardEvaluationRules->evaluateHands($board, $playerHands);
    }
}
