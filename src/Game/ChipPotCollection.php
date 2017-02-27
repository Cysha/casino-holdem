<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Game\Chips;
use Illuminate\Support\Collection;

class ChipPotCollection extends Collection
{
    /**
     * @return Chips
     */
    public function total(): Chips
    {
        return Chips::fromAmount($this->sum(function (ChipPot $chipPot) {
            return $chipPot->total()->amount();
        }));
    }

    /**
     * @var ChipPot
     *
     * @return ChipPotCollection
     */
    public function remove(ChipPot $removeChipPot)
    {
        return $this->reject(function (ChipPot $chipPot) use ($removeChipPot) {
            return $chipPot->equals($removeChipPot);
        })->values();
    }
}
