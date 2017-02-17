<?php

namespace xLink\Poker;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Exceptions\TableException;
use xLink\Poker\Game\Dealer;
use xLink\Poker\Game\HandCollection;
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
     * @var int
     */
    private $button = 0;

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
     * @return int
     */
    public function button(): int
    {
        return $this->button;
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
        return $this->playersSatDown()->get($this->button);
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

    /**
     * @param Player $player
     *
     * @throws TableException
     */
    public function giveButtonToPlayer(Player $player)
    {
        $playerIndex = $this->playersSatDown()
            ->filter
            ->equals($player)
            ->keys()
            ->first();

        if ($playerIndex === null) {
            throw TableException::invalidButtonPosition();
        }

        $this->button = $playerIndex;
    }

    /**
     * Moves the button along the table seats.
     */
    public function moveButton()
    {
        ++$this->button;

        if ($this->button >= $this->playersSatDown()->count()) {
            $this->button = 0;
        }
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function findSeat(Player $findPlayer): int
    {
        return $this->players()->filter(function (Player $player) use ($findPlayer) {
            return $player->equals($findPlayer);
        })->keys()->first();
    }

    /**
     * @param string $playerName
     *
     * @return Player
     */
    public function findPlayerByName($playerName): Player
    {
        return $this->players()
            ->filter(function (Player $player) use ($playerName) {
                return $player->name() === $playerName;
            })
            ->first();
    }

    /**
     * @return HandCollection
     */
    public function dealCardsToPlayers(): HandCollection
    {
        $this->hands = HandCollection::make();

        $this->playersSatDown()->each(function (Player $player) {
            $this->hands->push(Hand::create(CardCollection::make([
                $this->dealer()->dealCard(),
            ]), $player));
        });

        // Because xLink wants it done "properly"... Cunt.
        $this->playersSatDown()->each(function (Player $player) {
            $this->hands->map(function (Hand $hand) use ($player) {
                if ($hand->player()->equals($player) === false) {
                    return false;
                }

                return $hand->addCard($this->dealer()->dealCard());
            });
        });

        return $this->hands;
    }
}
