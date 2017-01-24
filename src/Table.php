<?php

namespace xLink\Poker;

use xLink\Poker\Game\Dealer;
use xLink\Poker\Game\Player;
use xLink\Poker\Game\PlayerCollection;

class Table
{
    /**
     * @var Dealer
     */
    private $dealer;

    /**
     * @var PlayerCollection
     */
    private $playersSatDown;

    /**
     * @var PlayerCollection
     */
    private $playersSatOut;

    /**
     * Table constructor.
     *
     * @param Dealer           $dealer
     * @param PlayerCollection $players
     */
    private function __construct(Dealer $dealer, PlayerCollection $players)
    {
        $this->dealer = $dealer;
        $this->playersSatDown = $players;
        $this->playersSatOut = PlayerCollection::make();
    }

    /**
     * @param Dealer           $dealer
     * @param PlayerCollection $players
     *
     * @return Table
     */
    public static function setUp(Dealer $dealer, PlayerCollection $players): self
    {
        return new self($dealer, $players);
    }

    /**
     * @return Dealer
     */
    public function dealer(): Dealer
    {
        return $this->dealer;
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->playersSatDown;
    }

    /**
     * TODO: Track button on moving it through rounds.
     *
     * @return Player
     */
    public function locatePlayerWithButton(): Player
    {
        return $this->playersSatDown()->first();
    }

    /**
     * @param Player $player
     */
    public function sitPlayerOut(Player $player)
    {
        $this->playersSatOut = $this->playersSatOut->push($player);
    }

    /**
     * @return PlayerCollection
     */
    public function playersSatDown(): PlayerCollection
    {
        return $this->players()->diff($this->playersSatOut)->values();
    }
}
