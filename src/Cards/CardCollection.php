<?php

namespace xLink\Poker\Cards;

use Illuminate\Support\Collection;

class CardCollection extends Collection
{
    /**
     * @param $name
     *
     * @return static
     */
    public function whereSuit($name)
    {
        $name = rtrim($name, 's');

        return $this->filter(function (Card $card) use ($name) {
            return $card->suit()->equals(call_user_func(Suit::class.'::'.$name));
        });
    }

    /**
     * @param $value
     *
     * @return static
     */
    public function whereValue($value)
    {
        return $this->filter(function (Card $card) use ($value) {
            return $card->value() === $value;
        });
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return CardCollection
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['hearts', 'diamonds', 'clubs', 'spades']) !== false) {
            return $this->whereSuit($name);
        }
    }
}
