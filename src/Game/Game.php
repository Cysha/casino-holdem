<?php

namespace Cysha\Casino\Holdem\Game;

use Ramsey\Uuid\UuidInterface;
use Cysha\Casino\Holdem\Client;

interface Game
{
    /**
     * @return UuidInterface
     */
    public function id(): UuidInterface;

    public function name(): string;

    public function players(): PlayerCollection;

    public function registerPlayer(Client $client, Chips $buyInAmount = null);

    public function tables(): TableCollection;

    public function __toString(): string;
}
