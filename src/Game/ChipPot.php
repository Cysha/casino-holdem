<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\ChipStackCollection;
use Cysha\Casino\Game\Contracts\Player;
use Cysha\Casino\Game\PlayerCollection;

class ChipPot
{
    /**
     * @var ChipStackCollection
     */
    private $chips;

    /**
     * @var PlayerCollection
     */
    private $players;

    private function __construct()
    {
        $this->chips = new ChipStackCollection();
        $this->players = new PlayerCollection();
    }

    /**
     * @return ChipPot
     */
    public static function create(): ChipPot
    {
        return new self();
    }

    /**
     * @param Chips  $chips
     * @param Player $player
     *
     * @return ChipPot
     */
    public function addChips(Chips $chips, Player $player): ChipPot
    {
        $existingChips = $this->chips()->get($player->name()) ?? Chips::zero();

        $this->chips->put($player->name(), Chips::fromAmount($existingChips->amount() + $chips->amount()));

        if ($this->players()->findByName($player->name()) === null) {
            $this->players->push($player);
        }

        return $this;
    }

    /**
     * @return ChipStackCollection
     */
    public function chips(): ChipStackCollection
    {
        return $this->chips;
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->players;
    }

    /**
     * @return Chips
     */
    public function total(): Chips
    {
        return $this->chips->total();
    }

    /**
     * @return int
     */
    public function totalAmount(): int
    {
        return $this->chips->total()->amount();
    }

    /**
     * @param ChipPot $object
     *
     * @return bool
     */
    public function equals(ChipPot $object): bool
    {
        return static::class === get_class($object)
        && $this->chips() === $object->chips()
        && $this->players() === $object->players()
        && $this->total() === $object->total();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $players = $this->players()
            ->map(function (Player $player) {
                return $player->name();
            })
            ->implode(', ');

        return sprintf('[%d] [%s]', $this->total()->amount(), $players);
    }
}
