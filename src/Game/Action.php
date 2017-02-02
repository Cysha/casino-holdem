<?php

namespace xLink\Poker\Game;

class Action
{
    const BB = 6;
    const SB = 5;
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
     * @car Chips
     */
    private $chips;

    public function __construct(Player $player, int $action, Chips $chips = null)
    {
        $this->player = $player;
        $this->action = $action;
        $this->chips = $chips ?? Chips::zero();
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
        switch ($this->action) {
            case 6:
                return sprintf('%s has posted Big Blind (%d).', $this->player->name(), $this->chips->amount());
            break;

            case 5:
                return sprintf('%s has posted Small Blind (%d).', $this->player->name(), $this->chips->amount());
            break;

            case 4:
                return sprintf('%s has pushed ALL IN (%d).', $this->player->name(), $this->chips->amount());
            break;

            case 3:
                return sprintf('%s has folded.', $this->player->name());
            break;

            case 2:
                return sprintf('%s has raised %d.', $this->player->name(), $this->chips->amount());
            break;

            case 1:
                return sprintf('%s has called %d.', $this->player->name(), $this->chips->amount());
            break;

            case 0:
                return sprintf('%s has checked.', $this->player->name());
            break;

            default:
                return sprintf('%s did something...', $this->player->name());
            break;
        }
    }

    public function __toString()
    {
        return $this->toString();
    }
}
