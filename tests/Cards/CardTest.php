<?php

namespace xLink\Tests\Cards;

use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Suit;

class CardTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * @expectedException InvalidArgumentException
     * @test
     */
    public function cannot_give_card_invalid_value()
    {
        $card = new Card(123, Suit::club());
    }

    /** @test **/
    public function can_get_card_identifier()
    {
        $suit = Suit::club();
        $card = new Card(Card::JACK, $suit);

        $this->assertEquals($card, $card->name().$card->suit()->symbol());
    }

    /** @test **/
    public function can_access_suit_data_for_cards()
    {
        $suit = Suit::club();
        $card = new Card(Card::ACE, $suit);

        $this->assertEquals($card->suit()->value(), $suit->value());
        $this->assertEquals($card->suit()->name(), $suit->name());
        $this->assertEquals($card->suit()->symbol(), $suit->symbol());
        $this->assertEquals($card->suitName(), $suit->name());
    }

    /** @test **/
    public function can_recognise_face_cards()
    {
        $card = new Card(Card::KING, Suit::diamond());
        $this->assertTrue($card->isKing());
        $this->assertTrue($card->isFaceCard());

        $card = new Card(Card::QUEEN, Suit::diamond());
        $this->assertTrue($card->isQueen());
        $this->assertTrue($card->isFaceCard());

        $card = new Card(Card::JACK, Suit::diamond());
        $this->assertTrue($card->isJack());
        $this->assertTrue($card->isFaceCard());

        $card = new Card(Card::ACE, Suit::diamond());
        $this->assertTrue($card->isACE());
        $this->assertTrue($card->isFaceCard());
    }

    /** @test **/
    public function test_number_card()
    {
        $card = new Card(10, Suit::diamond());
        $this->assertTrue($card->isNumberCard());

        $card = new Card(Card::ACE, Suit::diamond());
        $this->assertFalse($card->isNumberCard());
    }

    /** @test **/
    public function test_not_face_card()
    {
        $card = new Card(5, Suit::diamond());

        $this->assertFalse($card->isAce());
        $this->assertFalse($card->isKing());
        $this->assertFalse($card->isQueen());
        $this->assertFalse($card->isJack());
        $this->assertFalse($card->isFaceCard());
    }
}
