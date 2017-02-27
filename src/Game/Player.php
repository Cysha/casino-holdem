<?php

namespace Cysha\Casino\Holdem\Game;

use Assert\Assertion;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Contracts\Player as PlayerContract;

class Player extends Client implements PlayerContract
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
     * @return PlayerContract
     */
    public static function fromClient(Client $client, Chips $chipCount = null): PlayerContract
    {
        return new self($client->name(), $client->wallet(), $chipCount);
    }

    /**
     * @param PlayerContract $object
     *
     * @return bool
     */
    public function equals(PlayerContract $object): bool
    {
        return static::class === get_class($object)
        && $this->name() === $object->name()
        && $this->wallet() === $object->wallet()
        && $this->chipStack() === $object->chipStack();
    }

    /**
     * @return Chips
     */
    public function chipStack(): Chips
    {
        return $this->chipStack;
    }

    /**
     * @param Chips $chips
     */
    public function bet(Chips $chips)
    {
        Assertion::greaterOrEqualThan($chips->amount(), 0);
        $this->chipStack()->subtract($chips);
    }
}
