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
        $deck                = new Deck();
        $dealer              = Dealer::startWork($deck, $cardEvaluationRules);

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

        $winningHand = CardCollection::fromString('3s 3d Jd Jh Qh');
        $expectedResult = SevenCardResult::createTwoPair($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function dealer_can_compare_4_hands_to_select_winnner()
    {
        $board = CardCollection::fromString('Ts 9h Qs Ks Js');
        $player1 = Hand::fromString('As 3d');
        $player2 = Hand::fromString('9s 9d');
        $player3 = Hand::fromString('Ah 9c');
        $player4 = Hand::fromString('Qh Qd');

        $result = Dealer::evaluateHands($board, $player1, $player2, $player3, $player4);

        $winningHand = CardCollection::fromString('Ts Js Qs Ks 14s');
        $expectedResult = SevenCardResult::createRoyalFlush($winningHand);
        $this->assertEquals($expectedResult, $result->first());
    }

    /** @test */
    public function dealer_can_compare_2_hands_as_pairs_and_decide_its_a_split_pot()
    {
        $board = CardCollection::fromString('As 3d 9s 2c Th');
        $player1 = Hand::fromString('Qh Qd');
        $player2 = Hand::fromString('Qs Qc');
        $player3 = Hand::fromString('6s 4c');

        $result = Dealer::evaluateHands($board, $player1, $player2, $player3);

        $this->assertCount(2, $result);
    }
}
