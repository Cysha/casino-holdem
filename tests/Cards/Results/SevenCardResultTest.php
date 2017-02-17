<?php

namespace Cysha\Casino\Holdem\Tests\Cards\Results;

use Cysha\Casino\Holdem\Cards\Card;
use Cysha\Casino\Holdem\Cards\CardCollection;
use Cysha\Casino\Holdem\Cards\Hand;
use Cysha\Casino\Holdem\Cards\Results\SevenCardResult;
use Cysha\Casino\Holdem\Client;
use Cysha\Casino\Holdem\Game\Chips;
use Cysha\Casino\Holdem\Game\Player;

class SevenCardResultTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test */
    public function royal_flush_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('10c Jc Qc Kc Ac');

        $expectedResult = SevenCardResult::createRoyalFlush($expected, $hand);

        $this->assertEquals(SevenCardResult::ROYAL_FLUSH, $expectedResult->rank());
        $this->assertEquals([0], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Royal Flush', $expectedResult->definition());
    }

    /** @test */
    public function straight_flush_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('9c Tc Jc Qc Kc');

        $expectedResult = SevenCardResult::createStraightFlush($expected, $hand);

        $this->assertEquals(SevenCardResult::STRAIGHT_FLUSH, $expectedResult->rank());
        $this->assertEquals([13], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Straight Flush to K', $expectedResult->definition());
    }

    /** @test */
    public function four_of_a_kind_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Tc Qc Qd Qh Qs');

        $expectedResult = SevenCardResult::createFourOfAKind($expected, $hand);

        $this->assertEquals(SevenCardResult::FOUR_OF_A_KIND, $expectedResult->rank());
        $this->assertEquals([12, 10], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('4 of a Kind - Qs', $expectedResult->definition());
    }

    /** @test */
    public function full_house_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Qd Qc Qc 8d 8h');

        $expectedResult = SevenCardResult::createFullHouse($expected, $hand);

        $this->assertEquals(SevenCardResult::FULL_HOUSE, $expectedResult->rank());
        $this->assertEquals([12, 8], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Full House - Qs over 8s', $expectedResult->definition());
    }

    /** @test */
    public function flush_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('7c Tc Jc Qc Kc');

        $expectedResult = SevenCardResult::createFlush($expected, $hand);

        $this->assertEquals(SevenCardResult::FLUSH, $expectedResult->rank());
        $this->assertEquals([13], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Flush to K', $expectedResult->definition());
    }

    /** @test */
    public function straight_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('5c 6h 7d 8c 9d');

        $expectedResult = SevenCardResult::createStraight($expected, $hand);

        $this->assertEquals(SevenCardResult::STRAIGHT, $expectedResult->rank());
        $this->assertEquals([9], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Straight to 9', $expectedResult->definition());
    }

    /** @test */
    public function straight_ace_high_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('10c Jc Qc Kc 14d');

        $expectedResult = SevenCardResult::createStraight($expected, $hand);

        $this->assertEquals(SevenCardResult::STRAIGHT, $expectedResult->rank());
        $this->assertEquals([14], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Straight to A', $expectedResult->definition());
    }

    /** @test */
    public function straight_ace_low_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Ad 2c 3c 4c 5d');

        $expectedResult = SevenCardResult::createStraight($expected, $hand);

        $this->assertEquals(SevenCardResult::STRAIGHT, $expectedResult->rank());
        $this->assertEquals([5], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Straight to 5', $expectedResult->definition());
    }

    /** @test */
    public function three_of_a_kind_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Qd Qs Qh 8d 10c');

        $expectedResult = SevenCardResult::createThreeOfAKind($expected, $hand);

        $this->assertEquals(SevenCardResult::THREE_OF_A_KIND, $expectedResult->rank());
        $this->assertEquals([12, 10], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('3 of a Kind - Qs', $expectedResult->definition());
    }

    /** @test */
    public function two_pair_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Qd Qc 10c 8d 8h');

        $expectedResult = SevenCardResult::createTwoPair($expected, $hand);

        $this->assertEquals(SevenCardResult::TWO_PAIR, $expectedResult->rank());
        $this->assertEquals([12, 8, 10], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Two Pair - Qs and 8s', $expectedResult->definition());
    }

    /** @test */
    public function one_pair_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Qd Qc 10c 8d 7s');

        $expectedResult = SevenCardResult::createOnePair($expected, $hand);

        $this->assertEquals(SevenCardResult::ONE_PAIR, $expectedResult->rank());
        $this->assertEquals([12, 10], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('Pair of Qs', $expectedResult->definition());
    }

    /** @test */
    public function high_card_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('Qd Jc 10c 8d 7s');

        $expectedResult = SevenCardResult::createHighCard($expected, $hand);

        $this->assertEquals(SevenCardResult::HIGH_CARD, $expectedResult->rank());
        $this->assertEquals([7, 8, 10, 11, 12], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('High Card - Q', $expectedResult->definition());
    }

    /** @test */
    public function high_card_ace_result_test()
    {
        $player = Player::fromClient(Client::register('xLink', Chips::fromAmount(500)));
        $hand = Hand::createUsingString('Jc Kc', $player);

        $expected = CardCollection::fromString('8d 10c Jc Qd 14s');

        $expectedResult = SevenCardResult::createHighCard($expected, $hand);

        $this->assertEquals(SevenCardResult::HIGH_CARD, $expectedResult->rank());
        $this->assertEquals([8, 10, 11, 12, 14], $expectedResult->value());
        $this->assertEquals($expected->__toString(), $expectedResult->cards()->__toString());
        $this->assertEquals('High Card - A', $expectedResult->definition());
    }
}
