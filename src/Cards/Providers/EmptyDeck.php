<?php

namespace Cysha\Casino\Holdem\Cards\Providers;

use Cysha\Casino\Holdem\Cards\Contracts\CardProvider;

class EmptyDeck implements CardProvider
{
    public function getCards()
    {
        return [];
    }
}
