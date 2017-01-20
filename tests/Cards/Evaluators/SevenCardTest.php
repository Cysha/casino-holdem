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

    /** @test **/
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

        $result = SevenCard::royalFlush($board->merge($hand));

        $expected = new CardCollection([
            new Card(Card::ACE, Suit::club()),
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function non_straight_flush_is_caught_in_royal_flush()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(2, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(5, Suit::heart()),
            new Card(7, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $result = SevenCard::royalFlush($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function straight_flush_is_not_a_royal_flush()
    {
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(9, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $result = SevenCard::royalFlush($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_straight_flush()
    {
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(9, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $result = SevenCard::straightFlush($board->merge($hand));

        $expected = new CardCollection([
            new Card(9, Suit::club()),
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function flush_is_not_straight_flush()
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

        $result = SevenCard::straightflush($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function straight_is_not_straight_flush()
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

        $result = SevenCard::straightflush($board->merge($hand));

        $this->assertFalse($result);
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

        $result = SevenCard::flush($board->merge($hand));

        $expected = new CardCollection([
            new Card(7, Suit::club()),
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
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

        $result = SevenCard::straight($board->merge($hand));

        $expected = new CardCollection([
            new Card(5, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(7, Suit::diamond()),
            new Card(8, Suit::club()),
            new Card(9, Suit::diamond()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function hand_evals_to_straight_with_high_ace()
    {
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]);

        $result = SevenCard::straight($board->merge($hand));

        $expected = new CardCollection([
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::KING, Suit::club()),
            new Card(Card::ACE_HIGH, Suit::diamond()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function hand_evals_to_straight_with_low_ace()
    {
        $board = new CardCollection([
            new Card(5, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(2, Suit::club()),
            new Card(3, Suit::club()),
        ]);

        $result = SevenCard::straight($board->merge($hand));

        $expected = new CardCollection([
            new Card(Card::ACE, Suit::diamond()),
            new Card(2, Suit::club()),
            new Card(3, Suit::club()),
            new Card(4, Suit::club()),
            new Card(5, Suit::diamond()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }
}
