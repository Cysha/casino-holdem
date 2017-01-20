<?php

namespace xLink\Tests\Cards;

use xLink\Poker\Cards\Suit;

class SuitTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test **/
    public function can_create_suits()
    {
        $suit = Suit::club();
        $this->assertEquals(Suit::CLUB, $suit->value());
        $this->assertEquals('club', $suit->name());
        $this->assertEquals('♣', $suit->symbol());

        $suit = Suit::diamond();
        $this->assertEquals(Suit::DIAMOND, $suit->value());
        $this->assertEquals('diamond', $suit->name());
        $this->assertEquals('♦', $suit->symbol());

        $suit = Suit::heart();
        $this->assertEquals(Suit::HEART, $suit->value());
        $this->assertEquals('heart', $suit->name());
        $this->assertEquals('♥', $suit->symbol());

        $suit = Suit::spade();
        $this->assertEquals(Suit::SPADE, $suit->value());
        $this->assertEquals('spade', $suit->name());
        $this->assertEquals('♠', $suit->symbol());
    }

    /** @test **/
    public function multiple_suit_calls_return_same_object()
    {
        $suit1 = Suit::spade();
        $suit2 = Suit::spade();

        $this->assertSame($suit1, $suit2);
    }

    /** @test **/
    public function can_get_suit_identifier()
    {
        $suit = Suit::spade();
        $this->assertEquals('♠', $suit);

        $suit = Suit::diamond();
        $this->assertEquals('♦', $suit);
    }
}
