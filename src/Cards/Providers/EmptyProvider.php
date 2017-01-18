<?php

namespace xLink\Poker\Cards\Providers;

use xLink\Poker\Cards\Contracts\CardProvider;

class EmptyProvider implements CardProvider
{
    public function getCards()
    {
        return  [];
    }
}
