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
