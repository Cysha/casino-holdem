<?php

namespace xLink\Poker\Cards;

use InvalidArgumentException;
use xLink\Poker\Exceptions\CardException;

class Card
{
    const ACE      = 1;
    const JACK     = 11;
    const QUEEN    = 12;
    const KING     = 13;
    const ACE_HIGH = 14;

    /**
     * The Suit of this card.
     */
    protected $suit;

    /**
     * The Value of this card.
     */
    protected $value;

    /**
     * @param int  $value the value of the card
     * @param Suit $suit  the suit of the card
     *
     * @throws InvalidArgumentException
     */
    public function __construct($value, Suit $suit)
    {
        $this->value = $value;

        if (!$this->isValidCardValue()) {
            throw new InvalidArgumentException('Invalid card value given: '.$value);
        }

        $this->suit = $suit;
    }

    /**
     * Get the Face value of the card.
     *
     * @return int face value
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Get the Suit of the card.
     *
     * @return Suit
     */
    public function suit()
    {
        return $this->suit;
    }

    /**
     * @return bool
     */
    protected function isValidCardValue()
    {
        if ($this->isNumberCard()) {
            return true;
        }

        if ($this->isFaceCard()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isNumberCard()
    {
        $values = range(2, 10);

        return in_array($this->value(), $values, true);
    }

    /**
     * @return bool
     */
    public function isFaceCard()
    {
        $values = [
            $this->isJack(),
            $this->isQueen(),
            $this->isKing(),
            $this->isAce(),
        ];

        return in_array(true, $values, true);
    }

    /*
     * Is this an Ace
     *
     * @return bool
     */
    public function isAce()
    {
        return $this->value === static::ACE || $this->value === self::ACE_HIGH;
    }

    /**
     * Is this a King.
     *
     * @return bool
     */
    public function isKing()
    {
        return $this->value === static::KING;
    }

    /**
     * Is this a Queen.
     *
     * @return bool
     */
    public function isQueen()
    {
        return $this->value === static::QUEEN;
    }

    /**
     * Is this a Jack.
     *
     * @return bool
     */
    public function isJack()
    {
        return $this->value === static::JACK;
    }

    /**
     * Gets human readable value for a given card.
     *
     * @return string
     */
    public function name()
    {
        if ($this->isAce()) {
            return 'A';
        }
        if ($this->isKing()) {
            return 'K';
        }
        if ($this->isQueen()) {
            return 'Q';
        }
        if ($this->isJack()) {
            return 'J';
        }
        if ($this->value() === 10) {
            return 'T';
        }

        return (string)$this->value();
    }

    /**
     * @param mixed $card
     *
     * @return bool
     */
    public function equals($card)
    {
        return get_class($card) === static::class
            && $card->value === $this->value;
    }

    /**
     * Returns a human readable string for outputting the card.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s%s', $this->name(), $this->suit()->symbol());
    }
}
