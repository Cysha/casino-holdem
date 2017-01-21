<?php

namespace xLink\Poker\Cards;

use BadMethodCallException;
use Illuminate\Support\Collection;

class CardCollection extends Collection
{
    public static function fromString($cards)
    {
        $cards = explode(' ', $cards);

        return static::make($cards)->map(function ($card) {
            return Card::fromString($card);
        });
    }

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
    public function sortBySuitValue()
    {
        return $this->sortBy(function (Card $card) {
            return $card->suit()->value();
        });
    }

    /**
     * @param int $sort
     *
     * @return CardCollection
     */
    public function sortByValue($sort = SORT_NUMERIC)
    {
        return $this
            ->groupBy(function (Card $card) {
                return $card->value();
            })
            ->map(function (CardCollection $valueGroup) {
                return $valueGroup->sortBySuitValue();
            })
            ->flatten()
            ->sortBy(function (Card $card) {
                return $card->value();
            }, $sort)
            ->values();
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
