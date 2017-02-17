<?php

namespace Cysha\Casino\Holdem\Game;

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

    /**
     * @param int $actionValue
     *
     * @return bool
     */
    public function hasAction(int $actionValue): bool
    {
        $count = $this
            ->filter(function (Action $action) use ($actionValue) {
                return $action->action() === $actionValue;
            })
            ->count();

        return $count > 0 ? true : false;
    }
}
