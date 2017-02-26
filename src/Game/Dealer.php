<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Cards\Card;
use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Contracts\CardEvaluator;
use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Cards\HandCollection;
use Cysha\Casino\Cards\ResultCollection;
use Cysha\Casino\Game\Contracts\Dealer as DealerContract;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Game\Contracts\Player as PlayerContract;
use Cysha\Casino\Holdem\Exceptions\RoundException;

class Dealer implements DealerContract
{
    /**
     * @var Deck
     */
    private $deck;

    /**
     * @var CardEvaluator
     */
    private $cardEvaluationRules;

    /**
     * @var CardCollection
     */
    private $communityCards;

    /**
     * @var CardCollection
     */
    private $burnCards;

    /**
     * @var HandCollection
     */
    private $hands;

    /**
     * Dealer constructor.
     *
     * @param Deck          $deck
     * @param CardEvaluator $cardEvaluationRules
     */
    private function __construct(Deck $deck, CardEvaluator $cardEvaluationRules)
    {
        $this->deck = $deck;
        $this->cardEvaluationRules = $cardEvaluationRules;

        $this->communityCards = CardCollection::make();
        $this->burnCards = CardCollection::make();
        $this->hands = HandCollection::make();
    }

    /**
     * @param Deck          $deck
     * @param CardEvaluator $cardEvaluationRules
     *
     * @return Dealer
     */
    public static function startWork(Deck $deck, CardEvaluator $cardEvaluationRules)
    {
        return new self($deck, $cardEvaluationRules);
    }

    /**
     * @return Deck
     */
    public function deck(): Deck
    {
        return $this->deck;
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
     * @return Card
     */
    public function dealCard(): Card
    {
        return $this->deck()->draw();
    }

    /**
     * @return HandCollection
     */
    public function hands(): HandCollection
    {
        return $this->hands;
    }

    /**
     * Shuffles the deck.
     */
    public function shuffleDeck()
    {
        $this->deck()->shuffle();
    }

    /**
     * Adds a card to the BurnCards(), also Adds a card to the CommunityCards().
     *
     * @param int $cards
     */
    public function dealCommunityCards(int $cards = 1)
    {
        // burn one
        $this->burnCards()->push($this->dealCard());

        // deal
        for ($i = 0; $i < $cards; ++$i) {
            $this->communityCards()->push($this->dealCard());
        }
    }

    /**
     * Deals the remainder of the community cards, whilst taking burn cards into account.
     */
    public function checkCommunityCards()
    {
        if ($this->communityCards()->count() === 5) {
            return;
        }

        if ($this->communityCards()->count() === 0) {
            $this->dealCommunityCards(3);
        }

        if ($this->communityCards()->count() === 3) {
            $this->dealCommunityCards(1);
        }

        if ($this->communityCards()->count() === 4) {
            $this->dealCommunityCards(1);
        }
    }

    /**
     * Deal the hands to the players.
     */
    public function dealHands(PlayerCollection $players)
    {
        $this->hands = $this->dealCardsToPlayers($players);
    }

    /**
     * @param PlayerContract $player
     *
     * @return Hand
     */
    public function playerHand(PlayerContract $player): Hand
    {
        $hand = $this->hands()->findByPlayer($player);

        if ($hand === null) {
            throw RoundException::playerHasNoHand($player);
        }

        return $hand;
    }

    /**
     * @return HandCollection
     */
    public function dealCardsToPlayers(PlayerCollection $players): HandCollection
    {
        $hands = HandCollection::make();

        // deal to the player after the button first
        $players
            ->each(function (PlayerContract $player) use ($hands) {
                $hands->push(Hand::create(CardCollection::make([
                    $this->dealCard(),
                ]), $player));
            })
            ->each(function (PlayerContract $player) use ($hands) {
                $hands->map(function (Hand $hand) use ($player, $hands) {
                    if ($hand->player()->equals($player) === false) {
                        return false;
                    }

                    return $hand->addCard($this->dealCard());
                });
            });

        return $hands;
    }

    /**
     * @param CardCollection $board
     * @param HandCollection $playerHands
     *
     * @return ResultCollection
     */
    public function evaluateHands(CardCollection $board, HandCollection $playerHands): ResultCollection
    {
        return $this->cardEvaluationRules->evaluateHands($board, $playerHands);
    }
}
