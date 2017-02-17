<?php

namespace Cysha\Casino\Holdem\Game;

use Illuminate\Support\Collection;

class ChipPotCollection extends Collection
{
    public function total(): Chips
    {
        return Chips::fromAmount($this->sum(function (ChipPot $chipPot) {
            return $chipPot->total()->amount();
        }));
    }
}
