<?php

namespace xLink\Tests\Cards\Evaluators;

use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Suit;

class SevenCardTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @te43st **/
    public function hand_evals_to_royal_flush()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $cards = $board->merge($hand);
        $eval = SevenCard::royalFlush($cards);

        $this->assertInstanceOf(CardCollection::class, $eval);
    }

    /** @te43st **/
    public function hand_evals_to_straight_flush()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(9, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $cards = $board->merge($hand);
        $eval = SevenCard::straightFlush($cards);

        $this->assertInstanceOf(CardCollection::class, $eval);
    }

    /** @test **/
    public function hand_evals_to_flush()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(7, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $cards = $board->merge($hand);
        $eval = SevenCard::flush($cards);

        $this->assertInstanceOf(CardCollection::class, $eval);
    }

    /** @test **/
    public function hand_evals_to_straight()
    {
        $board = new CardCollection([
            new Card(9, Suit::diamond()),
            new Card(Card::ACE, Suit::club()),
            new Card(2, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(8, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(5, Suit::club()),
            new Card(7, Suit::diamond()),
        ]);

        $cards = $board->merge($hand);
        $eval = SevenCard::straight($cards);

        $expected = new CardCollection([
            new Card(5, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(7, Suit::diamond()),
            new Card(8, Suit::club()),
            new Card(9, Suit::diamond()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $eval);
        $this->assertEquals($eval, $expected);
    }
}
