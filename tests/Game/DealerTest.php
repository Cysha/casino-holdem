<?php

namespace xLink\Tests\Game;

use xLink\Poker\Cards\CardCollection;
use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Cards\Hand;
use xLink\Poker\Cards\Results\SevenCardResult;
use xLink\Poker\Client;
use xLink\Poker\Game\Chips;
use xLink\Poker\Game\Dealer;
use xLink\Poker\Game\HandCollection;
use xLink\Poker\Game\Player;

class DealerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /** @test */
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
        $client1 = Client::register('xLink', Chips::fromAmount(5000));
        $client2 = Client::register('jesus', Chips::fromAmount(5000));

        $player1 = Player::fromClient($client1);
        $player2 = Player::fromClient($client2);

        $board = CardCollection::fromString('Tc 6d Qh Jd 3s');
        $hand1 = Hand::createUsingString('Ks Kd', $player1);
        $hand2 = Hand::createUsingString('Jh 3d', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('3s 3d Jd Jh Qh');
        $expectedResult = SevenCardResult::createTwoPair($winningHand, $hand2);
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
        $hand1 = Hand::createUsingString('As 3d', $player1);
        $hand2 = Hand::createUsingString('9s 9d', $player2);
        $hand3 = Hand::createUsingString('Ah 9c', $player3);
        $hand4 = Hand::createUsingString('Qh Qd', $player4);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2, $hand3, $hand4]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Ts Js Qs Ks 14s');
        $expectedResult = SevenCardResult::createRoyalFlush($winningHand, $hand1);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function dealer_can_compare_10_hands_and_decide_its_a_split_pot()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));
        $player3 = Player::fromClient(Client::register('player3', Chips::fromAmount(500)));
        $player4 = Player::fromClient(Client::register('player4', Chips::fromAmount(500)));
        $player5 = Player::fromClient(Client::register('player5', Chips::fromAmount(500)));
        $player6 = Player::fromClient(Client::register('player6', Chips::fromAmount(500)));
        $player7 = Player::fromClient(Client::register('player7', Chips::fromAmount(500)));
        $player8 = Player::fromClient(Client::register('player8', Chips::fromAmount(500)));
        $player9 = Player::fromClient(Client::register('player9', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As Ah Ac Ad Kd');
        $hand1 = Hand::createUsingString('2h 5s', $player1);
        $hand2 = Hand::createUsingString('9c 7s', $player2);
        $hand3 = Hand::createUsingString('5h 5d', $player3);
        $hand4 = Hand::createUsingString('8d Qh', $player4);
        $hand5 = Hand::createUsingString('Qs Qd', $player5);
        $hand6 = Hand::createUsingString('3d 6s', $player6);
        $hand7 = Hand::createUsingString('2c 5c', $player7);
        $hand8 = Hand::createUsingString('Th Jd', $player8);
        $hand9 = Hand::createUsingString('Ts 4c', $player9);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2, $hand3, $hand4, $hand5, $hand6, $hand7, $hand8, $hand9]));

        $this->assertCount(9, $result);
    }

    /** @test */
    public function dealer_can_compare_10_hands_with_odd_kickers_and_decide_its_a_split_pot()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));
        $player3 = Player::fromClient(Client::register('player3', Chips::fromAmount(500)));
        $player4 = Player::fromClient(Client::register('player4', Chips::fromAmount(500)));
        $player5 = Player::fromClient(Client::register('player5', Chips::fromAmount(500)));
        $player6 = Player::fromClient(Client::register('player6', Chips::fromAmount(500)));
        $player7 = Player::fromClient(Client::register('player7', Chips::fromAmount(500)));
        $player8 = Player::fromClient(Client::register('player8', Chips::fromAmount(500)));
        $player9 = Player::fromClient(Client::register('player9', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As Ah Ac Ad 2d');
        $hand1 = Hand::createUsingString('2h Ks', $player1);
        $hand2 = Hand::createUsingString('9c Kh', $player2);
        $hand3 = Hand::createUsingString('5h Kd', $player3);
        $hand4 = Hand::createUsingString('8d Qh', $player4);
        $hand5 = Hand::createUsingString('Qs Qd', $player5);
        $hand6 = Hand::createUsingString('3d 6s', $player6);
        $hand7 = Hand::createUsingString('2c 5c', $player7);
        $hand8 = Hand::createUsingString('Th Jd', $player8);
        $hand9 = Hand::createUsingString('Ts 4c', $player9);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2, $hand3, $hand4, $hand5, $hand6, $hand7, $hand8, $hand9]));

        $this->assertCount(3, $result);
    }

    /** @test */
    public function dealer_can_compare_2_hands_as_pairs_and_decide_its_a_split_pot()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));
        $player3 = Player::fromClient(Client::register('player3', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As 3d 9s 2c Th');
        $hand1 = Hand::createUsingString('Qh Qd', $player1);
        $hand2 = Hand::createUsingString('Qs Qc', $player2);
        $hand3 = Hand::createUsingString('6s 4c', $player3);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2, $hand3]));

        $this->assertCount(2, $result);

        // make sure both hands are the same
        $winningHand = CardCollection::fromString('Qh Qd 14s Th 9s');
        $expectedResult = SevenCardResult::createOnePair($winningHand, $hand1);
        $this->assertEquals($expectedResult, $result->first());

        $winningHand = CardCollection::fromString('Qs Qc 14s Th 9s');
        $expectedResult = SevenCardResult::createOnePair($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->last());
    }

    /** @test */
    public function dealer_can_compare_2_high_card_hands_and_decide_its_a_split_pot()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('5h 3s 7s 4c 6h');
        $hand1 = Hand::createUsingString('Kh Ah', $player1);
        $hand2 = Hand::createUsingString('Kc Qc', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(2, $result);

        // make sure both hands are the same
        $winningHand = CardCollection::fromString('3s 4c 5h 6h 7s');
        $expectedResult = SevenCardResult::createStraight($winningHand, $hand1);
        $this->assertEquals($expectedResult, $result->first());

        $winningHand = CardCollection::fromString('3s 4c 5h 6h 7s');
        $expectedResult = SevenCardResult::createStraight($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->last());
    }

    /** @test */
    public function when_comparing_2_quads_highest_quad_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('As Qd 2s 2c Qh');
        $hand1 = Hand::createUsingString('2h 2d', $player1);
        $hand2 = Hand::createUsingString('Qs Qc', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Qc Qd Qh Qs 14s');
        $expectedResult = SevenCardResult::createFourOfAKind($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_full_houses_highest_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('9s 2c Js Jh 2h');
        $hand1 = Hand::createUsingString('9c 9d', $player1);
        $hand2 = Hand::createUsingString('Ac Jc', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('Js Jh Jc 2c 2h');
        $expectedResult = SevenCardResult::createFullHouse($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_flushes_highest_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('5h 7h Jh 9h 5s');
        $hand1 = Hand::createUsingString('Kh Qs', $player1);
        $hand2 = Hand::createUsingString('Ah Tc', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('5h 7h 9h Jh 14h');
        $expectedResult = SevenCardResult::createFlush($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_straights_highest_wins()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('2d 3h 4c 5s 6h');
        $hand1 = Hand::createUsingString('7h 9s', $player1);
        $hand2 = Hand::createUsingString('Ah 5c', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('3h 4c 5s 6h 7h');
        $expectedResult = SevenCardResult::createStraight($winningHand, $hand1);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function compare_2_pair_as_counterfeit()
    {
        $player1 = Player::fromClient(Client::register('player1', Chips::fromAmount(500)));
        $player2 = Player::fromClient(Client::register('player2', Chips::fromAmount(500)));

        $board = CardCollection::fromString('4h 4s 9c 9s Tc');
        $hand1 = Hand::createUsingString('2h 2s', $player1);
        $hand2 = Hand::createUsingString('Qh 7c', $player2);

        $dealer = Dealer::startWork(new Deck(), new SevenCard());

        $result = $dealer->evaluateHands($board, HandCollection::make([$hand1, $hand2]));

        $this->assertCount(1, $result);

        $winningHand = CardCollection::fromString('9c 9s 4h 4s Qh');
        $expectedResult = SevenCardResult::createTwoPair($winningHand, $hand2);
        $this->assertEquals($expectedResult, $result->first());
    }
}
