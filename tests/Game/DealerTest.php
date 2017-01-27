<?php

namespace xLink\Tests\Cards\Evaluators;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Dealer;
use xLink\Poker\Game\Player;

class DealerTest extends \PHPUnit_Framework_TestCase
{
    public function dealer_can_start_work_with_a_deck_and_a_ruleset()
    {
        $cardEvaluationRules = new SevenCard();
        $deck = new Deck();
        $dealer = Dealer::startWork($deck, $cardEvaluationRules);

        $this->assertInstanceOf(Dealer::class, $dealer);
    }

    /** @test */
    public function dealer_can_compare_2_hands_to_select_winnner()
    {
        $board = CardCollection::fromString('Tc 6d Qh Jd 3s');

        $client1 = Client::register('xLink', Chips::fromAmount(5000));
        $client2 = Client::register('jesus', Chips::fromAmount(5000));

        $player1 = Player::fromClient($client1);
        $player2 = Player::fromClient($client2);

        $hand1 = Hand::createUsingString('Ks Kd', $player1);
        $hand2 = Hand::createUsingString('Jh 3d', $player2);

        $result = Dealer::evaluateHands($board, $hand1, $hand2);

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('3s 3d Jd Jh Qh');
        $expectedResult = SevenCardResult::createTwoPair($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function dealer_can_compare_4_hands_to_select_winnner()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));
        $player3 = Player::fromClient(Client::register('player3', Chips::fromAmount(500)));
        $player4 = Player::fromClient(Client::register('player4', Chips::fromAmount(500)));

        $board = CardCollection::fromString('Ts 9h Qs Ks Js');
        $player1 = Hand::createUsingString('As 3d', $player1);
        $player2 = Hand::createUsingString('9s 9d', $player2);
        $player3 = Hand::createUsingString('Ah 9c', $player3);
        $player4 = Hand::createUsingString('Qh Qd', $player4);

        $result = Dealer::evaluateHands($board, $player1, $player2, $player3, $player4);

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Ts Js Qs Ks 14s');
        $expectedResult = SevenCardResult::createRoyalFlush($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function dealer_can_compare_2_hands_as_pairs_and_decide_its_a_split_pot()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));
        $player3 = Player::fromClient(Client::register('player3', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As 3d 9s 2c Th');
        $player1 = Hand::createUsingString('Qh Qd', $player1);
        $player2 = Hand::createUsingString('Qs Qc', $player2);
        $player3 = Hand::createUsingString('6s 4c', $player3);

        $result = Dealer::evaluateHands($board, $player1, $player2, $player3);

        $this->assertCount(2, $result);

        // make sure both hands are the same
        $winningHand = CardCollection::fromString('Qh Qd 14s Th 9s');
        $expectedResult = SevenCardResult::createOnePair($winningHand);
        $this->assertEquals($expectedResult, $result->first());

        $winningHand = CardCollection::fromString('Qs Qc 14s Th 9s');
        $expectedResult = SevenCardResult::createOnePair($winningHand);
        $this->assertEquals($expectedResult, $result->last());
    }

    /** @test */
    public function when_comparing_2_quads_highest_quad_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As Qd 2s 2c Qh');
        $player1 = Hand::createUsingString('2h 2d', $player1);
        $player2 = Hand::createUsingString('Qs Qc', $player2);

        $result = Dealer::evaluateHands($board, $player1, $player2);

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Qc Qd Qh Qs 14s');
        $expectedResult = SevenCardResult::createFourOfAKind($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_full_houses_highest_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('9s 2c Js Jh 2h');
        $player1 = Hand::createUsingString('9c 9d', $player1);
        $player2 = Hand::createUsingString('Ac Jc', $player2);

        $result = Dealer::evaluateHands($board, $player1, $player2);

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Js Jh Jc 2c 2h');
        $expectedResult = SevenCardResult::createFullHouse($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_flushes_highest_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('5h 7h Jh 9h 5s');
        $player1 = Hand::createUsingString('Kh Qs', $player1);
        $player2 = Hand::createUsingString('Ah Tc', $player2);

        $result = Dealer::evaluateHands($board, $player1, $player2);

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('5h 7h 9h Jh 14h');
        $expectedResult = SevenCardResult::createFlush($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }
}
