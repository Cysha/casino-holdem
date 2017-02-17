<?php

namespace Cysha\Casino\Holdem\Game;

use Illuminate\Support\Collection;

class ChipStackCollection extends Collection
{
    public function total(): Chips
    {
        return Chips::fromAmount($this->sum(function (Chips $chips) {
            return $chips->amount();
        }));
    }

    /**
     * @return static
     */
    public function sortByChipAmount()
    {
        return self::make($this->sortBy->amount());
    }

    /**
     * @return Chips
     */
    public function findByPlayer(Player $player): Chips
    {
        return $this->get($player->name()) ?? Chips::zero();
    }
}
