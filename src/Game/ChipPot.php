<?php

namespace xLink\Poker\Game;

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
        $this->chips = ChipStackCollection::make();
        $this->players = PlayerCollection::make();
    }

    /**
     * @return ChipPot
     */
    public function create(): ChipPot
    {
        return new self();
    }

    /**
     * @return ChipPot
     */
    public function addChips(Chips $chips, Player $player): ChipPot
    {
        $this->chips->put($player->name(), $chips);
        $this->players->push($player);

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
}
