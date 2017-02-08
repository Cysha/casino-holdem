<?php

namespace xLink\Poker\Cards;

use xLink\Poker\Cards\Providers\StandardDeck;
use xLink\Poker\Cards\Contracts\CardProvider;

class Deck
{
    protected $cards = [];

    protected $cardsDrawn = [];

    public function __construct(CardProvider $provider = null)
    {
        if (is_null($provider)) {
            $provider = new StandardDeck();
        }

        $this->cards = $provider->getCards();
    }

    /**
     * Draw a card from the deck.
     *
     * @return Card
     *
     * @throws UnderflowException
     */
    public function draw()
    {
        if ($this->count() == 0) {
            throw new \UnderflowException('No more cards in the deck!');
        }

        $card = array_pop($this->cards);

        $this->cardsDrawn[] = $card;

        return $card;
    }

    /**
     * Draw a hand of cards from the deck.
     *
     * @param int $size the number of cards to draw
     *
     * @return array return array of Card
     *
     * @throws UnderflowException
     */
    public function drawHand($size = 1)
    {
        $hand = [];

        for ($i = 0; $i < $size; ++$i) {
            $hand[] = $this->draw();
        }

        return $hand;
    }

    /**
     * Get the cards in the deck.
     *
     * @return array array of Cards
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Get the cards in the deck.
     *
     * @return array array of Cards
     */
    public function getDrawnCards()
    {
        return $this->cardsDrawn;
    }

    /**
     * Get the number of cards in the deck.
     *
     * @return int
     */
    public function count()
    {
        return count($this->cards);
    }

    /**
     * Get the number of cards drawn.
     *
     * @return int
     */
    public function countDrawn()
    {
        return count($this->cardsDrawn);
    }

    /**
     * Reset the deck, and shuffles the cards.
     *
     * @return bool
     */
    public function shuffle()
    {
        $this->reset();

        return shuffle($this->cards);
    }

    /**
     * ReAdds cards from the drawn pile back to the main deck.
     */
    public function reset()
    {
        $this->cards = array_merge($this->cards, $this->cardsDrawn);
        $this->cardsDrawn = [];
    }
}
