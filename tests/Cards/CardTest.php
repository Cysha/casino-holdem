<?php

namespace xLink\Tests\Cards;

use InvalidArgumentException;
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
    }

    /** @test **/
    public function can_recognise_face_cards()
    {
        $card = new Card(Card::KING, Suit::diamond());
        $this->assertTrue($card->isKing());
        $this->assertTrue($card->isFaceCard());
        $this->assertEquals($card->name(), 'K');

        $card = new Card(Card::QUEEN, Suit::diamond());
        $this->assertTrue($card->isQueen());
        $this->assertTrue($card->isFaceCard());
        $this->assertEquals($card->name(), 'Q');

        $card = new Card(Card::JACK, Suit::diamond());
        $this->assertTrue($card->isJack());
        $this->assertTrue($card->isFaceCard());
        $this->assertEquals($card->name(), 'J');

        $card = new Card(Card::ACE, Suit::diamond());
        $this->assertTrue($card->isAce());
        $this->assertTrue($card->isFaceCard());
        $this->assertEquals($card->name(), 'A');
    }

    /** @test **/
    public function is_number_card()
    {
        $card = new Card(10, Suit::diamond());
        $this->assertTrue($card->isNumberCard());

        $card = new Card(Card::ACE, Suit::diamond());
        $this->assertFalse($card->isNumberCard());
    }

    /** @test **/
    public function is_not_face_card()
    {
        $card = new Card(5, Suit::diamond());

        $this->assertFalse($card->isAce());
        $this->assertFalse($card->isKing());
        $this->assertFalse($card->isQueen());
        $this->assertFalse($card->isJack());
        $this->assertFalse($card->isFaceCard());
    }

    /** @test */
    public function it_doesnt_consider_a_class_of_another_type_equal()
    {
        $suit = Suit::club();
        $card = new Card(Card::ACE, $suit);
        $otherCardValue = new Card(Card::KING, $suit);

        $this->assertFalse($card->equals($suit));
        $this->assertFalse($card->equals($otherCardValue));
    }

    /** @test */
    public function it_can_compare_an_equal_card()
    {
        $suit = Suit::club();
        $card = new Card(Card::ACE, $suit);
        $otherCardValue = new Card(Card::ACE, $suit);

        $this->assertTrue($card->equals($otherCardValue));
    }

    /** @test */
    public function can_create_card_from_string()
    {
        $builtCard = Card::fromString('8♦');

        $actualCard = new Card(8, Suit::diamond());

        $this->assertEquals($builtCard, $actualCard);
    }

    /** @test */
    public function can_create_card_from_lowercase_string()
    {
        $builtCard = Card::fromString('k♦');

        $actualCard = new Card(Card::KING, Suit::diamond());

        $this->assertEquals($builtCard, $actualCard);
    }

    /**
     * @expectedException \xLink\Poker\Exceptions\CardException
     * @test
     */
    public function cannot_create_card_from_invalid_suit_number()
    {
        Card::fromString('45');
    }

    /**
     * @expectedException \xLink\Poker\Exceptions\CardException
     * @test
     */
    public function cannot_create_card_from_invalid_card_number()
    {
        Card::fromString('Ld');
    }
}
