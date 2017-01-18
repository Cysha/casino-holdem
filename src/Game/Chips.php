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
    private function __construct(int $chips)
    {
        $this->chips = $chips;
    }

    /**
     * @return Chips
     */
    public static function zero(): self
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
    public function amount()
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
     * @return string
     */
    public function __toString()
    {
        return (string) $this->amount();
    }
}
