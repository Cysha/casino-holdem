<?php

namespace Cysha\Casino\Holdem\Cards\Contracts;

/**
 * A card provider. Provides the cards for the deck.
 */
interface CardProvider
{
    /**
     * Provides the cards for a deck.
     *
     * @return array an array of cards
     */
    public function getCards();
}
