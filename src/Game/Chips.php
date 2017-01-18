<?php

namespace xLink\Poker\Game;

class Chips
{
    /**
     * @var int
     */
    private $chips;

    /**
     * Chips constructor.
     *
     * @param int $chips
     */
    public function __construct(int $chips)
    {
        $this->chips = $chips;
    }

    public static function zero()
    {
        return new self(0);
    }

    /**
     * @param int $int
     *
     * @return Chips
     */
    public static function fromAmount(int $int): self
    {
        return new self($int);
    }

    /**
     * @return int
     */
    public function amount(): int
    {
        return $this->chips;
    }

    /**
     * @param Chips $chips
     */
    public function add(Chips $chips)
    {
        $this->chips += $chips->amount();
    }

    /**
     * @param Chips $chips
     */
    public function subtract(Chips $chips)
    {
        $this->chips -= $chips->amount();
    }

    /**
     *
     */
    public function __toString()
    {
        return $this->amount();
    }
}
