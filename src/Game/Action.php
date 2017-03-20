<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Contracts\Name as NameContract;
use InvalidArgumentException;

class Action
{
    const DEALT_RIVER = 9;
    const DEALT_TURN = 8;
    const DEALT_FLOP = 7;

    const BIG_BLIND = 6;
    const SMALL_BLIND = 5;

    const ALLIN = 4;
    const FOLD = 3;
    const RAISE = 2;
    const CALL = 1;
    const CHECK = 0;

    /**
     * @var
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

    public function __construct(NameContract $player, int $action, $attributes = [])
    {
        if (isset($attributes['chips']) && !($attributes['chips'] instanceof Chips)) {
            throw new InvalidArgumentException('Chip attribute should be instance of Chips');
        }

        if (isset($attributes['communityCards']) && !($attributes['communityCards'] instanceof CardCollection)) {
            throw new InvalidArgumentException('communityCards attribute should be instance of CardCollection');
        }

        $this->player = $player;
        $this->action = $action;
        $this->communityCards = $attributes['communityCards'] ?? CardCollection::make();
        $this->chips = $attributes['chips'] ?? Chips::zero();
    }

    /**
     * @return int
     */
    public function action(): int
    {
        return $this->action;
    }

    /**
     * @return
     */
    public function player()
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

    /**
     * @return CardCollection
     */
    public function communityCards(): CardCollection
    {
        return $this->communityCards;
    }

    public function toString()
    {
        $message = null;
        switch ($this->action) {
            case static::DEALT_RIVER:
                $message = sprintf('%s has dealt the river (%s).', $this->player->name(), $this->communityCards()->__toString());
                break;

            case static::DEALT_TURN:
                $message = sprintf('%s has dealt the turn (%s).', $this->player->name(), $this->communityCards()->__toString());
                break;

            case static::DEALT_FLOP:
                $message = sprintf('%s has dealt the flop (%s).', $this->player->name(), $this->communityCards()->__toString());
                break;

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
