<?php

namespace xLink\Poker\Cards;

use InvalidArgumentException;

class Card
{
    const ACE = 1;
    const JACK = 11;
    const QUEEN = 12;
    const KING = 13;

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
     * Get the suit name for the card.
     *
     * @return string
     */
    public function suitName()
    {
        return $this->suit->name();
    }

    /**
     * Get the suit symbol for the card.
     *
     * @return string
     */
    public function suitSymbol()
    {
        return $this->suit->symbol();
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
            static::JACK, static::QUEEN, static::KING, static::ACE,
        ];

        return in_array($this->value(), $values, true);
    }

    /*
     * Is this an Ace
     *
     * @return bool
     */
    public function isAce()
    {
        return $this->value === static::ACE;
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
        if ($this->value() <= 9) {
            return (string) $this->value();
        }
        if ($this->value() === 10) {
            return 'T';
        }
        if ($this->isJack()) {
            return 'J';
        }
        if ($this->isQueen()) {
            return 'Q';
        }
        if ($this->isKing()) {
            return 'K';
        }
        if ($this->isAce()) {
            return 'A';
        }
    }

    /**
     * Returns a human readable string for outputting the card.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s%s', $this->name(), $this->suitSymbol());
    }
}
