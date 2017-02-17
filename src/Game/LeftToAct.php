<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Game\Contracts\Player;
use Cysha\Casino\Game\PlayerCollection;
use Illuminate\Support\Collection;

class LeftToAct extends Collection
{
    const BIG_BLIND = 6;
    const SMALL_BLIND = 5;
    const ALL_IN = 3;
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
        $collection = $players->map(function (Player $player, $seatNumber) {
            // everyone is still left to act
            return [
                'seat' => $seatNumber,
                'player' => $player->name(),
                'action' => self::STILL_TO_ACT,
            ];
        });

        return self::make($collection->toArray());
    }

    /**
     * Resets all players, and move dealer to the bottom of queue.
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

        return self::make($collection->values()->toArray());
    }

    /**
     * @return LeftToAct
     */
    public function resetActions(): self
    {
        $collection = $this
            ->transform(function ($array) {
                if ($array['action'] !== self::ALL_IN) {
                    $array['action'] = self::STILL_TO_ACT;
                }

                return $array;
            })
            ->values();

        return self::make($collection->values()->toArray());
    }

    /**
     * @return LeftToAct
     */
    public function sortBySeats(): self
    {
        $collection = $this
            ->sortBy(function ($array) {
                return $array['seat'];
            }, SORT_NUMERIC)
            ->values();

        return self::make($collection->values()->toArray());
    }

    /**
     * @return LeftToAct
     */
    public function playerHasActioned(Player $player, int $value = 0): self
    {
        $collection = $this->movePlayerToLastInQueue();
        if (in_array($value, [self::AGGRESSIVELY_ACTIONED, self::ALL_IN])) {
            $collection = $collection->resetActions();
        }

        $collection->setActivity($player->name(), $value);

        return self::make($collection->values()->toArray());
    }

    /**
     * @param Player $player
     * @param int    $activity
     *
     * @return LeftToAct
     */
    public function setActivity($player, int $activity): self
    {
        // var_dump($this);
        $result = $this
            ->filter(function ($array) use ($player) {
                return $array['player'] === $player;
            })
        ;

        $array = $result->first();
        $array['action'] = $activity;
        $this->put($result->keys()->first(), $array);

        return self::make($this->values()->toArray());
    }

    /**
     * @return LeftToAct
     */
    public function movePlayerToLastInQueue(): self
    {
        $collection = $this->splice(1)->merge($this->splice(0, 1));

        return self::make($collection->values()->toArray());
    }

    /**
     * @return LeftToAct
     */
    public function resetPlayerListFromSeat(int $seatNumber): self
    {
        $firstPlayer = $this->first();
        if ($seatNumber === $firstPlayer['seat']) {
            return self::make($this->values()->toArray());
        }

        $new = $this->sortBy(function ($value) use ($seatNumber) {
            if ($value['seat'] < $seatNumber) {
                return ($value['seat'] + 1) * 10;
            }

            return $value['seat'];
        });

        return new self($new->values());
    }

    /**
     * @return LeftToAct
     */
    public function removePlayer(Player $player): self
    {
        $collection = $this
            ->reject(function ($array) use ($player) {
                return $array['player'] === $player->name();
            });

        return self::make($collection->values()->toArray());
    }

    /**
     * @return array
     */
    public function getNextPlayer()
    {
        return $this
            ->reject(function ($value) {
                return in_array($value['action'], [self::ACTIONED, self::AGGRESSIVELY_ACTIONED, self::ALL_IN]);
            })
            ->first()
        ;
    }
}
