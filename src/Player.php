<?php

namespace xLink\Poker;

class Player
{
    /**
     * @var string
     */
    private $name;

    /**
     * PlayerTest constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return Player
     */
    public static function register($name): Player
    {
        return new static($name);
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
