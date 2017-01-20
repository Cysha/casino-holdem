<?php

namespace xLink\Poker\Cards;

use BadMethodCallException;
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
     * @param $sort
     *
     * @return CardCollection
     */
    public function sortByValue($sort = SORT_NUMERIC)
    {
        return $this->sortBy(function (Card $card) {
            return $card->value();
        }, $sort)->values();
    }

    /**
     * Replaces any Aces found in the collection with values of 14.
     *
     * @return CardCollection
     */
    public function switchAceValue()
    {
        return $this->map(function (Card $card) {
            if ($card->isAce() === false) {
                return $card;
            }

            return new Card(14, $card->suit());
        });
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return CardCollection
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['hearts', 'diamonds', 'clubs', 'spades'], true) !== false) {
            return $this->whereSuit($name);
        }

        throw new BadMethodCallException();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->map(function (Card $card) {
            return $card->__toString();
        })->implode(' ');
    }
}
