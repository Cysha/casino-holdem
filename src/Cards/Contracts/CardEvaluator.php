<?php

namespace xLink\Poker\Cards\Contracts;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Game\HandCollection;

interface CardEvaluator
{
    /**
     * @param CardCollection $board
     * @param HandCollection $hands
     */
    public function evaluateHands(CardCollection $board, HandCollection $hands);
}
