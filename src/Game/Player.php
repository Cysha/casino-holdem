<?php

namespace xLink\Poker\Game;

use xLink\Poker\Client;

class Player extends Client
{
    /**
     * @var Chips
     */
    private $chipStack;

    /**
     * PlayerTest constructor.
     *
     * @param string $name
     * @param Chips  $chips
     */
    public function __construct($name, Chips $wallet = null, Chips $chips = null)
    {
        parent::__construct($name, $wallet);

        $this->chipStack = $chips ?? Chips::zero();
    }

    /**
     * @param Client $client
     * @param Chips  $chipCount
     *
     * @return Player
     */
    public static function fromClient(Client $client, Chips $chipCount = null): Player
    {
        return new self($client->name(), $client->wallet(), $chipCount);
    }

    public function chipStack(): Chips
    {
        return $this->chipStack;
    }
}
