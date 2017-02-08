<?php

namespace xLink\Poker\Game;

use Illuminate\Support\Collection;
use xLink\Poker\Cards\Hand;

class HandCollection extends Collection
{
    public function findByPlayer(Player $player)
    {
        return $this->first(function (Hand $hand) use ($player) {
            return $hand->player()->equals($player);
        });
    }
}
