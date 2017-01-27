<?php

namespace xLink\Poker\Game;

use Illuminate\Support\Collection;

class ChipStackCollection extends Collection
{
    public function total(): Chips
    {
        return Chips::fromAmount($this->sum(function (Chips $chips) {
            return $chips->amount();
        }));
    }
}
