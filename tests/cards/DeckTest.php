<?php

namespace xLink\Tests\Cards;

use xLink\Poker\Cards\Providers\EmptyProvider;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Deck;

class DeckTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test **/
    public function can_create_a_deck()
    {
        $deck = new Deck();

        $this->assertEquals(52, $deck->count());
    }

    /** @test **/
    public function can_draw_a_card()
    {
        $deck = new Deck();
        $card = $deck->draw();

        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals(51, $deck->count());
        $this->assertEquals(1, $deck->countDrawn());
    }

    /**
     * @expectedException UnderflowException
     * @test
     */
    public function cant_draw_a_card_from_empty_deck()
    {
        $deck = new Deck(new EmptyProvider());
        $deck->draw();
    }

    /** @test **/
    public function can_draw_a_hand()
    {
        $deck = new Deck();

        $hand = $deck->drawHand();
        $this->assertCount(1, $hand);

        $hand = $deck->drawHand(10);
        $this->assertCount(10, $hand);
    }

    /** @test **/
    public function can_reset_deck()
    {
        $deck = new Deck();
        $deck->drawHand(2);

        $this->assertCount(50, $deck->getCards());
        $this->assertCount(2, $deck->getDrawnCards());

        $deck->shuffle();

        $this->assertCount(52, $deck->getCards());
        $this->assertCount(0, $deck->getDrawnCards());
    }
}
