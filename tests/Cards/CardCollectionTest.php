<?php

namespace xLink\Tests\Cards\Evaluators;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Suit;

class CardCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function can_get_suit_counts_from_cards_given()
    {
        $cards = CardCollection::make([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $this->assertCount(1, $cards->diamonds());
        $this->assertCount(3, $cards->clubs());
        $this->assertCount(0, $cards->spades());
        $this->assertCount(1, $cards->hearts());

        $this->assertCount(1, $cards->whereSuit('diamonds'));
        $this->assertCount(3, $cards->whereSuit('clubs'));
        $this->assertCount(0, $cards->whereSuit('spades'));
        $this->assertCount(1, $cards->whereSuit('hearts'));
    }

    /** @test */
    public function can_get_value_counts_from_cards_given()
    {
        $cards = CardCollection::make([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $this->assertCount(2, $cards->whereValue(8));
        $this->assertCount(1, $cards->whereValue(10));
        $this->assertCount(1, $cards->whereValue(Card::ACE));
        $this->assertCount(1, $cards->whereValue(Card::QUEEN));
    }
}
