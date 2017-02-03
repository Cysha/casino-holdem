<?php

namespace xLink\Poker\Game;

use Illuminate\Support\Collection;

class LeftToAct extends Collection
{
    const BIG_BLIND = 5;
    const SMALL_BLIND = 4;
    const AGGRESSIVELY_ACTIONED = 2;
    const STILL_TO_ACT = 1;
    const ACTIONED = 0;

    /**
     * Sets all players actions to STILL_TO_ACT.
     *
     * @param PlayerCollection $players
     *
     * @return LeftToAct
     */
    public function setup(PlayerCollection $players): self
    {
        $collection = $players->map(function (Player $player) {
            // everyone is still left to act
            return [
                'player' => $player->name(),
                'action' => self::STILL_TO_ACT,
            ];
        });

        return self::make($collection->toArray());
    }

    /**
     * Resets all players, and move BTN/SB/BB to bottom of queue.
     *
     * @param PlayerCollection $players
     *
     * @return LeftToAct
     */
    public function setupWithoutDealer(PlayerCollection $players): self
    {
        $collection = $this->setup($players);

        // move the dealer to last
        $collection = $collection->movePlayerToLastInQueue();

        return self::make($collection->toArray());
    }

    /**
     * @param LeftToAct $collection
     *
     * @return LeftToAct
     */
    public function reset(LeftToAct $collection)
    {
        $collection = $collection
            ->map(function ($array) {
                return [
                    'player' => $array['player'],
                    'action' => self::STILL_TO_ACT,
                ];
            })
            ->toArray();

        return self::make($collection);
    }

    /**
     * @return self
     */
    public function playerHasActioned(int $value = 0): self
    {
        $collection = $this->movePlayerToLastInQueue();

        if ($value === self::AGGRESSIVELY_ACTIONED) {
            $collection = $this->reset($collection);
        }
        $collection->setActivity($collection->last()['player'], $value);

        return new self($collection->toArray());
    }

    /**
     * @param Player $player
     * @param int    $activity
     *
     * @return LeftToAct
     */
    public function setActivity($player, int $activity): self
    {
        $collection = $this->put($this->keys()->last(), [
            'player' => $player,
            'action' => $activity,
        ]);

        return new self($this);
    }

    /**
     * @return LeftToAct
     */
    public function movePlayerToLastInQueue(): self
    {
        return new self($this->splice(1)->merge($this->splice(0, 1)));
    }

    /**
     * @return LeftToAct
     */
    public function removePlayer(Player $player)
    {
        $collection = $this
            ->reject(function ($array) use ($player) {
                return $array['player'] === $player->name();
            })
            ->values()
            ->toArray();

        return new self($collection);
    }

    /**
     * @return string
     */
    public function getNextPlayer()
    {
        return $this
            ->reject(function ($value) {
                return in_array($value['action'], [self::ACTIONED, self::AGGRESSIVELY_ACTIONED]);
            })
            ->first()
        ;
    }
}
