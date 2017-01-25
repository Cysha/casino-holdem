<?php

namespace xLink\Tests\Cards\Evaluators;

use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Card;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Cards\Suit;

class SevenCardTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test **/
    public function can_eval_hand_royal_flush()
    {
        $board = CardCollection::fromString('8d 10c Ac 8h Qc');
        $hand = Hand::fromString('Jc Kc');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('10c Jc Qc Kc 14c');
        $expectedResult = SevenCardResult::createRoyalFlush($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function fix_issue_with_descending_order_flushes()
    {
        $board = CardCollection::fromString('Ts 9h Qs Ks Js');
        $hand = Hand::fromString('As 3d');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('10s Js Qs Ks 14s');
        $expectedResult = SevenCardResult::createRoyalFlush($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_straight_flush()
    {
        $board = CardCollection::fromString('6d Tc 9c 6h Qc');
        $hand = Hand::fromString('Jc Kc');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('9c Tc Jc Qc Kc');
        $expectedResult = SevenCardResult::createStraightFlush($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_four_of_a_kind()
    {
        $board = CardCollection::fromString('8d Qc Tc 2h Qd');
        $hand = Hand::fromString('Qs Qh');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Tc Qc Qd Qh Qs');
        $expectedResult = SevenCardResult::createFourOfAKind($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_full_house()
    {
        $board = CardCollection::fromString('8d Qc Tc 8h Qd');
        $hand = Hand::fromString('7s Qh');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd Qh 8d 8h');
        $expectedResult = SevenCardResult::createFullHouse($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_flush()
    {
        $board = CardCollection::fromString('8d Tc 7c 8h Qc');
        $hand = Hand::fromString('Jc Kc');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('7c Tc Jc Qc Kc');
        $expectedResult = SevenCardResult::createFlush($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_straight()
    {
        $board = CardCollection::fromString('9d Ac 2c 6h 8d');
        $hand = Hand::fromString('5c 7d');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('5c 6h 7d 8d 9d');
        $expectedResult = SevenCardResult::createStraight($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_three_of_a_kind()
    {
        $board = CardCollection::fromString('8d 3c 10c 2h Qd');
        $hand = Hand::fromString('Qs Qh');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('8d 10c Qd Qh Qs');
        $expectedResult = SevenCardResult::createThreeOfAKind($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_two_pair()
    {
        $board = CardCollection::fromString('8d Qc 10c 8h Qd');
        $hand = Hand::fromString('7s 6h');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd 8d 8h 10c');
        $expectedResult = SevenCardResult::createTwoPair($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_one_pair()
    {
        $board = CardCollection::fromString('8d Qc 10c 2h Qd');
        $hand = Hand::fromString('7s 6h');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('Qc Qd 10c 8d 7s');
        $expectedResult = SevenCardResult::createOnePair($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_high_card()
    {
        $board = CardCollection::fromString('8d Jc 10c 2h Qd');
        $hand = Hand::fromString('7s 6h');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('7s 8d 10c Jc Qd');
        $expectedResult = SevenCardResult::createHighCard($expected);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test **/
    public function can_eval_hand_ace_high_card()
    {
        $board = CardCollection::fromString('8d Jc 10c 2h Qd');
        $hand = Hand::fromString('As 6h');

        $result = SevenCard::evaluate($board, $hand);

        $expected = CardCollection::fromString('8d 10c Jc Qd 14s');
        $expectedResult = SevenCardResult::createHighCard($expected);
        $this->assertEquals($expectedResult, $result);
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
    public function flush_is_not_a_royal_flush()
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

        $result = SevenCard::straightFlush($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function straight_and_flush_dont_always_make_straight_flush()
    {
        $board = new CardCollection([
            new Card(Card::KING, Suit::spade()),
            new Card(9, Suit::spade()),
            new Card(Card::JACK, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
            new Card(10, Suit::heart()),
        ]);

        $hand = new Hand([
            new Card(Card::ACE, Suit::spade()),
            new Card(3, Suit::spade()),
        ]);

        $result = SevenCard::straightFlush($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_four_of_a_kind()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]);

        $result = SevenCard::fourOfAKind($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::KING, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]);

        $result = SevenCard::fourOfAKind($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_full_house()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]);

        $result = SevenCard::fullHouse($board->merge($hand));

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

        $result = SevenCard::fullHouse($board->merge($hand));

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
    public function hand_evals_to_flush_ace_high()
    {
        $board = new CardCollection([
            new Card(Card::KING, Suit::diamond()),
            new Card(Card::QUEEN, Suit::diamond()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(8, Suit::diamond()),
            new Card(9, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(10, Suit::diamond()),
            new Card(4, Suit::diamond()),
        ]);

        $result = SevenCard::flush($board->merge($hand));

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

    /** @test */
    public function hand_doesnt_eval_to_straight_without_10_or_5()
    {
        $board = new CardCollection([
            new Card(6, Suit::diamond()),
            new Card(9, Suit::club()),
            new Card(Card::ACE, Suit::diamond()),
            new Card(4, Suit::club()),
            new Card(Card::QUEEN, Suit::club()),
        ]);

        $hand = new Hand([
            new Card(2, Suit::club()),
            new Card(3, Suit::club()),
        ]);

        $result = SevenCard::straight($board->merge($hand));
        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_three_of_a_kind()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(3, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(Card::QUEEN, Suit::spade()),
            new Card(Card::QUEEN, Suit::heart()),
        ]);

        $result = SevenCard::threeOfAKind($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(8, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::twoPair($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::twoPair($board->merge($hand));

        $this->assertFalse($result);
    }

    /** @test **/
    public function hand_evals_to_one_pair()
    {
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::QUEEN, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::onePair($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::highCard($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(Card::ACE, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::highCard($board->merge($hand));

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
        $board = new CardCollection([
            new Card(8, Suit::diamond()),
            new Card(Card::JACK, Suit::club()),
            new Card(10, Suit::club()),
            new Card(2, Suit::heart()),
            new Card(Card::QUEEN, Suit::diamond()),
        ]);

        $hand = new Hand([
            new Card(7, Suit::spade()),
            new Card(6, Suit::heart()),
        ]);

        $result = SevenCard::onePair($board->merge($hand));

        $this->assertFalse($result);
    }
}
