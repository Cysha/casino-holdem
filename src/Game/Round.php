<?php

namespace xLink\Poker\Game;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Exceptions\RoundException;
use xLink\Poker\Table;

class Round
{
    /**
     * @var Table
     */
    private $table;

    /**
     * @var ChipStackCollection
     */
    private $chipStacks;

    /**
     * @var HandCollection
     */
    private $hands = null;

    /**
     * @var PlayerCollection
     */
    private $foldedPlayers;

    /**
     * Round constructor.
     *
     * @param Table $table
     */
    private function __construct(Table $table)
    {
        $this->table = $table;
        $this->chipStacks = ChipStackCollection::make();
        $this->hands = HandCollection::make();
        $this->foldedPlayers = PlayerCollection::make();

        // init the chipstack for each player with zero chips
        $this->players()->each(function (Player $player) {
            $this->chipStacks->put($player->name(), Chips::zero());
        });
    }

    /**
     * @param Table $table
     *
     * @return Round
     */
    public static function start(Table $table): Round
    {
        return new static($table);
    }

    /**
     * @return PlayerCollection
     */
    public function playersStillIn(): PlayerCollection
    {
        return $this->table->playersSatDown()->diff($this->foldedPlayers());
    }

    /**
     * @return PlayerCollection
     */
    public function foldedPlayers(): PlayerCollection
    {
        return $this->foldedPlayers;
    }

    /**
     * @param Player $actualPlayer
     *
     * @return bool
     */
    public function playerIsStillIn(Player $actualPlayer)
    {
        return $this->playersStillIn()->filter(function (Player $player) use ($actualPlayer) {
            return $player->name() === $actualPlayer->name();
        })->count() === 1;
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->table->players();
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return $this->table;
    }

    /**
     * @return Player
     */
    public function playerWithButton(): Player
    {
        return $this->table()->locatePlayerWithButton();
    }

    /**
     * @return Player
     */
    public function playerWithSmallBlind(): Player
    {
        if ($this->table()->playersSatDown()->count() === 2) {
            return $this->table()->playersSatDown()->get(0);
        }

        return $this->table()->playersSatDown()->get(1);
    }

    /**
     * @return Player
     */
    public function playerWithBigBlind(): Player
    {
        if ($this->table()->playersSatDown()->count() === 2) {
            return $this->table()->playersSatDown()->get(1);
        }

        return $this->table()->playersSatDown()->get(2);
    }

    /**
     * @param Player $player
     */
    public function postSmallBlind(Player $player)
    {
        // Take chips from player
        $chips = $this->smallBlind();

        $this->postBlind($player, $chips);
    }

    /**
     * @param Player $player
     */
    public function postBigBlind(Player $player)
    {
        // Take chips from player
        $chips = $this->bigBlind();

        $this->postBlind($player, $chips);
    }

    /**
     * @return Chips
     */
    private function smallBlind(): Chips
    {
        return Chips::fromAmount(25);
    }

    /**
     * @return Chips
     */
    private function bigBlind(): Chips
    {
        return Chips::fromAmount(50);
    }

    /**
     * @param Player $player
     *
     * @return Chips
     */
    public function playerChipCount(Player $player): Chips
    {
        return $this->chipStacks->get($player->name()) ?? Chips::zero();
    }

    /**
     * @return ChipStackCollection
     */
    public function chipStacks(): ChipStackCollection
    {
        return $this->chipStacks;
    }

    /**
     * @param Player $player
     * @param Chips  $chips
     */
    private function postBlind(Player $player, $chips)
    {
        $player->chipStack()->subtract($chips);

        // Add chips to player's table stack
        $this->chipStacks->put($player->name(), $chips);
    }

    /**
     * Deal the hands to the players.
     */
    public function dealHands()
    {
        $this->hands = HandCollection::make();

        $this->table()->playersSatDown()->each(function (Player $player) {
            $this->hands->push(Hand::create(CardCollection::make([
                $this->table()->dealer()->dealCard(),
            ]), $player));
        });

        // Because xLink wants it done "properly"... Cunt.
        $this->table()->playersSatDown()->each(function (Player $player) {
            $this->hands->map(function (Hand $hand) use ($player) {
                if ($hand->player()->equals($player) === false) {
                    return false;
                }

                return $hand->addCard($this->table()->dealer()->dealCard());
            });
        });
    }

    /**
     * @param Player $player
     *
     * @return Hand
     */
    public function playerHand(Player $player): Hand
    {
        $hand = $this->hands->first(function (Hand $hand) use ($player) {
            return $player->name() === $hand->player()->name();
        });

        if ($hand === null) {
            throw RoundException::playerHasNoHand($player);
        }

        return $hand;
    }

    /**
     * @return Player
     */
    public function whosTurnIsIt(): Player
    {
        return $this->table()->playersSatDown()->get(3);
    }

    /**
     * @param Player $player
     */
    public function playerCalls(Player $player)
    {
        $chips = $this->highestBet();

        $this->placeChipBet($player, $chips);
    }

    /**
     * @param Player $player
     * @param Chips  $chips
     */
    public function playerRaises(Player $player, Chips $chips)
    {
        $this->placeChipBet($player, $chips);
    }

    /**
     * @param Player $player
     */
    public function playerFoldsHand(Player $player)
    {
        if ($this->playerIsStillIn($player) === false) {
            throw RoundException::playerHasNoActiveHand($player);
        }

        $this->foldedPlayers->push($player);

        // transfer their table()->chipStack() to the round->pot(default)
    }

    /**
     * @param Player $player
     */
    public function playerPushesAllIn(Player $player)
    {
        $this->placeChipBet($player, $player->chipStack());
    }

    /**
     * @return Chips
     */
    private function highestBet(): Chips
    {
        return Chips::fromAmount($this->chipStacks()->max(function (Chips $chips) {
            return $chips->amount();
        }) ?? 0);
    }

    /**
     * @param Player $player
     * @param Chips  $chips
     */
    private function placeChipBet(Player $player, Chips $chips)
    {
        if ($player->chipStack()->amount() < $chips->amount()) {
            throw RoundException::notEnoughChipsInChipStack($player, $chips);
        }

        // add the chips to the players tableStack first
        $this->playerChipCount($player)->add($chips);

        // then remove it off their actual stack
        $player->bet($chips);
    }
}
