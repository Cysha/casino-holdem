<?php

namespace xLink\Poker\Cards\Providers;

use xLink\Poker\Cards\Contracts\CardProvider;

class EmptyDeck implements CardProvider
{
    public function getCards()
    {
        return [];
    }
}
