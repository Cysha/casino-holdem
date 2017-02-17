<?php

namespace Cysha\Casino\Holdem\Cards\Contracts;

use Cysha\Casino\Holdem\Cards\CardCollection;
use Cysha\Casino\Holdem\Game\HandCollection;

interface CardEvaluator
{
    /**
     * @param CardCollection $board
     * @param HandCollection $hands
     */
    public function evaluateHands(CardCollection $board, HandCollection $hands);
}
