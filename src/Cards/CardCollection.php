<?php

namespace Cysha\Casino\Holdem\Cards;

use BadMethodCallException;
use Illuminate\Support\Collection;

class CardCollection extends Collection
{
    /**
     * @var string
     *
     * @return static
     */
    public static function fromString(string $cards)
    {
        $cards = explode(' ', $cards);

        return static::make($cards)->map(function ($card) {
            return Card::fromString($card);
        });
    }

    /**
     * Get cards where Suit value is...
     *
     * @param string $name
     *
     * @return static
     */
    public function whereSuit(string $name)
    {
        $name = rtrim($name, 's');

        return $this->filter(function (Card $card) use ($name) {
            return $card->suit()->equals(call_user_func(Suit::class.'::'.$name));
        });
    }

    /**
     * Get cards where Card Value is..
     *
     * @param int $value
     *
     * @return static
     */
    public function whereValue(int $value)
    {
        return $this->filter(function (Card $card) use ($value) {
            return $card->value() === $value;
        });
    }

    /**
     * Sort cards by Suit value.
     *
     * @param $sort
     *
     * @return static
     */
    public function sortBySuitValue()
    {
        return $this->sortBy(function (Card $card) {
            return $card->suit()->value();
        });
    }

    /**
     * Sum cards by their value.
     *
     * @param int $sort
     *
     * @return static
     */
    public function sumByValue()
    {
        return $this->sum(function (Card $card) {
            return $card->value();
        });
    }

    /**
     * Sort cards by their value, and then by Suit.
     *
     * @param int $sort
     *
     * @return static
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
     * Group cards by the value and sort by highest card count first.
     *
     * @return static
     */
    public function groupByValue()
    {
        return $this
            ->sortByDesc(function (Card $card) {
                return $card->value();
            }, SORT_NUMERIC)
            ->groupBy(function (Card $card) {
                return $card->value();
            })
            ->sortByDesc(function ($group) {
                return count($group);
            }, SORT_NUMERIC)
            ->values();
    }

    /**
     * Replaces any Aces found in the collection with values of 14.
     *
     * @return static
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
     * @return static
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
