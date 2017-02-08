<?php

namespace xLink\Poker\Game;

use Illuminate\Support\Collection;

class ActionCollection extends Collection
{
    public function all()
    {
        return $this
            ->map(function (Action $action) {
                return $action->toString();
            })
            ->toArray();
    }
}
