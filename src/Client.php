<?php

namespace Cysha\Casino\Holdem;

use Cysha\Casino\Holdem\Game\Chips;

class Client
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Chips
     */
    private $wallet;

    /**
     * ClientTest constructor.
     *
     * @param string $name
     * @param Chips  $chips
     */
    public function __construct($name, Chips $wallet = null)
    {
        $this->name = $name;
        $this->wallet = $wallet ?? Chips::zero();
    }

    /**
     * @param string $name
     * @param Chips  $chips
     *
     * @return Client
     */
    public static function register($name, Chips $chips = null): Client
    {
        return new static($name, $chips);
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Chips
     */
    public function wallet(): Chips
    {
        return $this->wallet;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name();
    }
}
