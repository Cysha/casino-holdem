<?php

namespace Cysha\Casino\Holdem\Game;

use Illuminate\Support\Collection;
use Cysha\Casino\Holdem\Cards\Hand;

class HandCollection extends Collection
{
    public function findByPlayer(Player $player)
    {
        return $this->first(function (Hand $hand) use ($player) {
            return $hand->player()->equals($player);
        });
    }
}
