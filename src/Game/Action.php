<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Game\Chips;

class Action
{
    const BIG_BLIND = 6;
    const SMALL_BLIND = 5;
    const ALLIN = 4;
    const FOLD = 3;
    const RAISE = 2;
    const CALL = 1;
    const CHECK = 0;

    /**
     * @var Player
     */
    private $player;

    /**
     * @var int
     */
    private $action;

    /**
     * @var Chips
     */
    private $chips;

    public function __construct(Player $player, int $action, Chips $chips = null)
    {
        $this->player = $player;
        $this->action = $action;
        $this->chips = $chips ?? Chips::zero();
    }

    /**
     * @return int
     */
    public function action(): int
    {
        return $this->action;
    }

    /**
     * @return Player
     */
    public function player(): Player
    {
        return $this->player;
    }

    /**
     * @return Chips
     */
    public function chips(): Chips
    {
        return $this->chips;
    }

    public function toString()
    {
        $message = null;
        switch ($this->action) {
            case static::BIG_BLIND:
                $message = sprintf('%s has posted Big Blind (%d).', $this->player->name(), $this->chips->amount());
            break;

            case static::SMALL_BLIND:
                $message = sprintf('%s has posted Small Blind (%d).', $this->player->name(), $this->chips->amount());
            break;

            case static::ALLIN:
                $message = sprintf('%s has pushed ALL IN (%d).', $this->player->name(), $this->chips->amount());
            break;

            case static::FOLD:
                $message = sprintf('%s has folded.', $this->player->name());
            break;

            case static::RAISE:
                $message = sprintf('%s has raised %d.', $this->player->name(), $this->chips->amount());
            break;

            case static::CALL:
                $message = sprintf('%s has called %d.', $this->player->name(), $this->chips->amount());
            break;

            case static::CHECK:
                $message = sprintf('%s has checked.', $this->player->name());
            break;
        }

        return $message;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
