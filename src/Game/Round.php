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
    private $betStacks;

    /**
     * @var CardCollection
     */
    private $burnCards;

    /**
     * @var CardCollection
     */
    private $communityCards;

    /**
     * @var HandCollection
     */
    private $hands;

    /**
     * @var PlayerCollection
     */
    private $foldedPlayers;

    /**
     * @var Chips
     */
    private $currentPot;

    /**
     * @var ActionCollection
     */
    private $playerActions;

    /**
     * @var PlayerCollection
     */
    private $leftToAct;

    /**
     * @var Player
     */
    private $winningPlayer = null;

    /**
     * Round constructor.
     *
     * @param Table $table
     */
    private function __construct(Table $table)
    {
        $this->table = $table;
        $this->betStacks = ChipStackCollection::make();
        $this->hands = HandCollection::make();
        $this->communityCards = CardCollection::make();
        $this->burnCards = CardCollection::make();
        $this->foldedPlayers = PlayerCollection::make();
        $this->currentPot = Chips::zero();
        $this->playerActions = ActionCollection::make();
        $this->leftToAct = LeftToAct::make();

        $this->table()->dealer()->shuffleDeck();

        // init the betStacks and actions for each player
        $this->resetBetStacks();
        $this->setupLeftToAct();
    }

    /**
     * Start a Round of poker.
     *
     * @param Table $table
     *
     * @return Round
     */
    public static function start(Table $table): Round
    {
        return new static($table);
    }

    /**
     * Run the cleanup procedure for an end of Round.
     */
    public function end()
    {
        $this->collectChipTotal();

        $this->determineWinningHands();
        $this->distributeWinnings();
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->table->players();
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
     * @return CardCollection
     */
    public function communityCards(): CardCollection
    {
        return $this->communityCards;
    }

    /**
     * @return CardCollection
     */
    public function burnCards(): CardCollection
    {
        return $this->burnCards;
    }

    /**
     * @return ActionCollection
     */
    public function playerActions(): ActionCollection
    {
        return $this->playerActions;
    }

    /**
     * @return LeftToAct
     */
    public function leftToAct(): LeftToAct
    {
        return $this->leftToAct;
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return $this->table;
    }

    /**
     * @param Player $actualPlayer
     *
     * @return bool
     */
    public function playerIsStillIn(Player $actualPlayer)
    {
        $playerCount = $this->playersStillIn()->filter->equals($actualPlayer)->count();

        return $playerCount === 1;
    }

    /**
     * @return Player
     */
    public function playerWithButton(): Player
    {
        return $this->table()->locatePlayerWithButton();
    }

    /**
     * TODO: Fix to make use of locatePlayerWithButton().
     *
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
     * TODO: Fix to make use of locatePlayerWithButton().
     *
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

        $this->playerActions()->push(new Action($player, Action::SMALL_BLIND, $this->smallBlind()));
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::SMALL_BLIND);
    }

    /**
     * @param Player $player
     */
    public function postBigBlind(Player $player)
    {
        // Take chips from player
        $chips = $this->bigBlind();

        $this->postBlind($player, $chips);

        $this->playerActions()->push(new Action($player, Action::BIG_BLIND, $this->bigBlind()));
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::BIG_BLIND);
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
     * @return Chips
     */
    public function currentPot(): Chips
    {
        return $this->currentPot;
    }

    /**
     * @param Player $player
     *
     * @return Chips
     */
    public function playerBetStack(Player $player): Chips
    {
        return $this->betStacks->get($player->name()) ?? Chips::zero();
    }

    /**
     * @return ChipStackCollection
     */
    public function betStacks(): ChipStackCollection
    {
        return $this->betStacks;
    }

    /**
     * @param Player $player
     * @param Chips  $chips
     */
    private function postBlind(Player $player, $chips)
    {
        $player->chipStack()->subtract($chips);

        // Add chips to player's table stack
        $this->betStacks->put($player->name(), $chips);
    }

    /**
     * Deal the hands to the players.
     */
    public function dealHands()
    {
        $this->hands = $this->table()->dealCardsToPlayers();
    }

    /**
     * @param Player $player
     *
     * @return Hand
     */
    public function playerHand(Player $player): Hand
    {
        $hand = $this->hands->findByPlayer($player);

        if ($hand === null) {
            throw RoundException::playerHasNoHand($player);
        }

        return $hand;
    }

    /**
     * @return Player|false
     */
    public function whosTurnIsIt()
    {
        $nextPlayer = $this->leftToAct()->getNextPlayer();

        return $this->players()->filter(function (Player $player) use ($nextPlayer) {
            return $player->name() === $nextPlayer['player'];
        })->first() ?? false;
    }

    /**
     * @return int
     */
    public function betStacksTotal(): int
    {
        return $this->betStacks()->total()->amount();
    }

    /**
     * @return Chips
     */
    public function collectChipTotal(): Chips
    {
        $amount = $this->betStacksTotal();
        $this->resetBetStacks();

        $this->currentPot->add(Chips::fromAmount($amount));

        return $this->currentPot;
    }

    /**
     * Deal the Flop.
     */
    public function dealFlop()
    {
        if ($this->communityCards()->count() !== 0) {
            throw RoundException::flopHasBeenDealt();
        }
        if ($player = $this->whosTurnIsIt()) {
            throw RoundException::playerStillNeedsToAct($player);
        }

        $this->collectChipTotal();
        $this->leftToAct = $this->leftToAct->setup($this->playersStillIn());

        // burn one
        $this->burnCards->push($this->table()->dealer()->dealCard());

        // deal 3
        $this->communityCards->push($this->table()->dealer()->dealCard());
        $this->communityCards->push($this->table()->dealer()->dealCard());
        $this->communityCards->push($this->table()->dealer()->dealCard());
    }

    /**
     * Deal the turn card.
     */
    public function dealTurn()
    {
        if ($this->communityCards()->count() !== 3) {
            throw RoundException::turnHasBeenDealt();
        }
        if ($player = $this->whosTurnIsIt()) {
            throw RoundException::playerStillNeedsToAct($player);
        }

        $this->dealCommunityCard();
    }

    /**
     * Deal the river card.
     */
    public function dealRiver()
    {
        if ($this->communityCards()->count() !== 4) {
            throw RoundException::riverHasBeenDealt();
        }
        if ($player = $this->whosTurnIsIt()) {
            throw RoundException::playerStillNeedsToAct($player);
        }

        $this->dealCommunityCard();
    }

    /**
     * Adds a card to the BurnCards(), also Adds a card to the CommunityCards().
     */
    private function dealCommunityCard()
    {
        $this->collectChipTotal();
        $this->leftToAct = $this->leftToAct->setup($this->playersStillIn());

        // burn one
        $this->burnCards->push($this->table()->dealer()->dealCard());

        // deal
        $this->communityCards->push($this->table()->dealer()->dealCard());
    }

    /**
     * @throws RoundException
     */
    public function checkPlayerTryingToAct(Player $player)
    {
        $actualPlayer = $this->whosTurnIsIt();
        if ($player !== $actualPlayer) {
            throw RoundException::playerTryingToActOutOfTurn($player, $actualPlayer);
        }
    }

    /**
     * @param Player $player
     *
     * @throws RoundException
     */
    public function playerCalls(Player $player)
    {
        $this->checkPlayerTryingToAct($player);

        $chips = $this->highestBet();

        // current highest bet - currentPlayersChipStack
        $amountLeftToBet = Chips::fromAmount($chips->amount() - $this->playerBetStack($player)->amount());

        $this->playerActions->push(new Action($player, Action::CALL, $amountLeftToBet));

        $this->placeChipBet($player, $amountLeftToBet);
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::ACTIONED);
    }

    /**
     * @param Player $player
     * @param Chips  $chips
     *
     * @throws RoundException
     */
    public function playerRaises(Player $player, Chips $chips)
    {
        $this->checkPlayerTryingToAct($player);

        $this->playerActions->push(new Action($player, Action::RAISE, $chips));

        $this->placeChipBet($player, $chips);
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::AGGRESSIVELY_ACTIONED);
    }

    /**
     * @param Player $player
     *
     * @throws RoundException
     */
    public function playerFoldsHand(Player $player)
    {
        $this->checkPlayerTryingToAct($player);

        $this->playerActions()->push(new Action($player, Action::FOLD));

        $this->foldedPlayers->push($player);
        $this->leftToAct = $this->leftToAct->removePlayer($player);
    }

    /**
     * @param Player $player
     *
     * @throws RoundException
     */
    public function playerPushesAllIn(Player $player)
    {
        $this->checkPlayerTryingToAct($player);

        $chips = $player->chipStack();

        // gotta create a new chip obj here cause of PHPs /awesome/ objRef ability :D
        $this->playerActions()->push(new Action($player, Action::ALLIN, Chips::fromAmount($chips->amount())));

        $this->placeChipBet($player, $chips);
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::AGGRESSIVELY_ACTIONED);
    }

    /**
     * @param Player $player
     *
     * @throws RoundException
     */
    public function playerChecks(Player $player)
    {
        $this->checkPlayerTryingToAct($player);

        $this->playerActions()->push(new Action($player, Action::CHECK));
        $this->leftToAct = $this->leftToAct->playerHasActioned(LeftToAct::ACTIONED);
    }

    /**
     * @return Chips
     */
    private function highestBet(): Chips
    {
        return Chips::fromAmount($this->betStacks()->max(function (Chips $chips) {
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
        $this->playerBetStack($player)->add($chips);

        // then remove it off their actual stack
        $player->bet($chips);
    }

    /**
     * Reset the chip stack for all players.
     */
    private function resetBetStacks()
    {
        $this->players()->each(function (Player $player) {
            $this->betStacks->put($player->name(), Chips::zero());
        });
    }

    /**
     * Reset the leftToAct collection.
     */
    private function setupLeftToAct()
    {
        if ($this->players()->count() > 2) {
            $this->leftToAct = $this->leftToAct->setupWithoutDealer($this->players());

            return;
        }

        $this->leftToAct = $this->leftToAct->setup($this->players());
    }

    /**
     * @param Player $player
     */
    public function sitPlayerOut(Player $player)
    {
        $this->table()->sitPlayerOut($player);
        $this->leftToAct = $this->leftToAct()->removePlayer($player);
    }

    /**
     * @return Player
     */
    private function determineWinningHands(): Player
    {
        $winningResults = $this->table()->dealer()->evaluateHands($this->communityCards, $this->hands);
        $winningHands = $winningResults->map->hand();
        $this->winningPlayer = $winningHands->first()->player();

        return $this->winningPlayer;
    }

    /**
     * Moves the chips from the currentPot to the players ChipStack.
     */
    private function distributeWinnings()
    {
        $this->winningPlayer->chipStack()->add($this->currentPot);
        $this->currentPot = Chips::zero();
    }

    /**
     * @return Player
     */
    public function winningPlayer(): Player
    {
        if ($this->winningPlayer === null) {
            throw RoundException::callingWinnerBeforeRoundEnd();
        }

        return $this->winningPlayer;
    }
}
