<?php

namespace Cysha\Casino\Holdem\Game\Parameters;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Contracts\GameParameters;
use Ramsey\Uuid\Uuid;

class CashGameParameters extends DefaultParameters implements GameParameters
{
    /**
     * @var Chips
     */
    private $smallBlind;

    /**
     * @var Chips
     */
    private $bigBlind;

    /**
     * @var int
     */
    private $tableSize = 9;

    /**
     * @var Chips
     */
    private $minimumBuyIn;

    /**
     * @var Chips
     */
    private $maximumBuyIn;

    public function __construct(Uuid $gameId, Chips $bigBlind, Chips $smallBlind = null, int $tableSize = 9, Chips $minimumBuyIn, Chips $maximumBuyIn = null)
    {
        parent::__construct($gameId, $bigBlind, $smallBlind, $tableSize);

        $this->minimumBuyIn = $minimumBuyIn;
        $this->maximumBuyIn = $maximumBuyIn;
    }

    /**
     * @return Chips
     */
    public function minimumBuyIn(): Chips
    {
        return $this->minimumBuyIn;
    }

    /**
     * @return Chips
     */
    public function maximumBuyIn(): Chips
    {
        return $this->maximumBuyIn ?? Chips::zero();
    }
}
