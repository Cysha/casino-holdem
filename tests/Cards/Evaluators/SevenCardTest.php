<?php

namespace xLink\Tests\Cards\Evaluators;

use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Cards\Suit;
use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Player;

class SevenCardTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test **/
    public function can_eval_hand_royal_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d 10c Ac 8h Qc');
        $hand = Hand::createUsingString('Jc Kc', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('10c Jc Qc Kc 14c');
        $expectedResult = SevenCardResult::createRoyalFlush($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function fix_issue_with_descending_order_flushes()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('Ts 9h Qs Ks Js');
        $hand = Hand::createUsingString('As 3d', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('10s Js Qs Ks 14s');
        $expectedResult = SevenCardResult::createRoyalFlush($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_straight_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('6d Tc 9c 6h Qc');
        $hand = Hand::createUsingString('Jc Kc', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('9c Tc Jc Qc Kc');
        $expectedResult = SevenCardResult::createStraightFlush($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_four_of_a_kind()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Qc Tc 2h Qd');
        $hand = Hand::createUsingString('Qs Qh', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Tc Qc Qd Qh Qs');
        $expectedResult = SevenCardResult::createFourOfAKind($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_full_house()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Qc Tc 8h Qd');
        $hand = Hand::createUsingString('7s Qh', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd Qh 8d 8h');
        $expectedResult = SevenCardResult::createFullHouse($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Tc 7c 8h Qc');
        $hand = Hand::createUsingString('Jc Kc', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('7c Tc Jc Qc Kc');
        $expectedResult = SevenCardResult::createFlush($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_straight()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('9d Ac 2c 6h 8d');
        $hand = Hand::createUsingString('5c 7d', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('5c 6h 7d 8d 9d');
        $expectedResult = SevenCardResult::createStraight($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_straight_on_the_board()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('3d 4c 5c 6h 7d');
        $hand = Hand::createUsingString('Kc Ad', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('3d 4c 5c 6h 7d');
        $expectedResult = SevenCardResult::createStraight($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_three_of_a_kind()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d 3c 10c 2h Qd');
        $hand = Hand::createUsingString('Qs Qh', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('8d 10c Qd Qh Qs');
        $expectedResult = SevenCardResult::createThreeOfAKind($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_two_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Qc 10c 8h Qd');
        $hand = Hand::createUsingString('7s 6h', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd 8d 8h 10c');
        $expectedResult = SevenCardResult::createTwoPair($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_one_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Qc 10c 2h Qd');
        $hand = Hand::createUsingString('7s 6h', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd 10c 8d 7s');
        $expectedResult = SevenCardResult::createOnePair($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_high_card()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Jc 10c 2h Qd');
        $hand = Hand::createUsingString('7s 6h', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('7s 8d 10c Jc Qd');
        $expectedResult = SevenCardResult::createHighCard($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_ace_high_card()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = CardCollection::fromString('8d Jc 10c 2h Qd');
        $hand = Hand::createUsingString('As 6h', $player);

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('8d 10c Jc Qd 14s');
        $expectedResult = SevenCardResult::createHighCard($expected, $hand);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function hand_evals_to_royal_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::royalFlush($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::KING, Suit::club()),
            new Card(Card::ACE_HIGH, Suit::club()),
        ]);
        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function straight_flush_is_not_a_royal_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(2, Suit::club()),
            new Card(Card::ACE, Suit::club()),
            new Card(5, Suit::heart()),
            new Card(7, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::royalFlush($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function flush_is_not_a_royal_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(9, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::royalFlush($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_straight_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(9, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::straightFlush($board->merge($hand->cards()), $hand);

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
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(7, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::straightflush($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function straight_is_not_straight_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(9, Suit::diamond()),
            new Card(Card::ACE, Suit::club()),
            new Card(2, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(8, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(5, Suit::club()),
            new Card(7, Suit::diamond()),
        ]), $player);

        $result = SevenCard::straightFlush($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function straight_and_flush_dont_always_make_straight_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(Card::KING, Suit::spade()),
            new Card(9, Suit::spade()),
            new Card(Card::JACK, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
            new Card(10, Suit::heart()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::ACE, Suit::spade()),
            new Card(3, Suit::spade()),
        ]), $player);

        $result = SevenCard::straightFlush($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_four_of_a_kind()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]), $player);

        $result = SevenCard::fourOfAKind($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(10, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::QUEEN, Suit::heart()),
            new Card(Card::QUEEN, Suit::spade()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function three_of_a_kind_is_not_four_of_a_kind()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::KING, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]), $player);

        $result = SevenCard::fourOfAKind($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_full_house()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]), $player);

        $result = SevenCard::fullHouse($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::QUEEN, Suit::heart()),
            new Card(8, Suit::diamond()),
            new Card(8, Suit::heart()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function flush_is_not_full_house()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(7, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::fullHouse($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_flush()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(7, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::flush($board->merge($hand->cards()), $hand);

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
    public function hand_evals_to_flush_ace_high()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(Card::KING, Suit::diamond()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(8, Suit::diamond()),
            new Card(9, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(10, Suit::diamond()),
            new Card(4, Suit::diamond()),
        ]), $player);

        $result = SevenCard::flush($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(9, Suit::diamond()),
            new Card(10, Suit::diamond()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::KING, Suit::diamond()),
            new Card(Card::ACE_HIGH, Suit::diamond()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function hand_evals_to_straight()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(9, Suit::diamond()),
            new Card(Card::ACE, Suit::club()),
            new Card(2, Suit::club()),
            new Card(6, Suit::heart()),
            new Card(8, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(5, Suit::club()),
            new Card(7, Suit::diamond()),
        ]), $player);

        $result = SevenCard::straight($board->merge($hand->cards()), $hand);

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
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::JACK, Suit::club()),
            new Card(Card::KING, Suit::club()),
        ]), $player);

        $result = SevenCard::straight($board->merge($hand->cards()), $hand);

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
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(5, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(2, Suit::club()),
            new Card(3, Suit::club()),
        ]), $player);

        $result = SevenCard::straight($board->merge($hand->cards()), $hand);

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

    /** @test */
    public function hand_doesnt_eval_to_straight_without_10_or_5()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(9, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(2, Suit::club()),
            new Card(3, Suit::club()),
        ]), $player);

        $result = SevenCard::straight($board->merge($hand->cards()), $hand);
        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_three_of_a_kind()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(3, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]), $player);

        $result = SevenCard::threeOfAKind($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::QUEEN, Suit::heart()),
            new Card(Card::QUEEN, Suit::spade()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function hand_evals_to_two_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::twoPair($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(8, Suit::diamond()),
            new Card(8, Suit::heart()),
            new Card(10, Suit::club()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function one_pair_is_not_two_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::twoPair($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_one_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::onePair($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(Card::QUEEN, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(8, Suit::diamond()),
            new Card(7, Suit::spade()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function hand_evals_to_high_card()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::highCard($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(7, Suit::spade()),
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function hand_evals_to_ace_high_card()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(Card::ACE, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::highCard($board->merge($hand->cards()), $hand);

        $expected = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(10, Suit::club()),
            new Card(Card::JACK, Suit::club()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::ACE_HIGH, Suit::spade()),
        ]);

        $this->assertInstanceOf(CardCollection::class, $result);
        $this->assertEquals($expected->__toString(), $result->__toString());
        $this->assertEquals($expected, $result);
    }

    /** @test **/
    public function high_card_is_not_one_pair()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = Hand::create(CardCollection::make([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]), $player);

        $result = SevenCard::onePair($board->merge($hand->cards()), $hand);

        $this->assertFalse($result);
    }
}
