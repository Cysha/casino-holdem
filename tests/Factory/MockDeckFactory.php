<?php

namespace Cysha\Casino\Holdem\Tests\Factory;

use Cysha\Casino\Cards\Card;
use Cysha\Casino\Cards\CardCollection;
use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Cards\Hand;
use Cysha\Casino\Cards\HandCollection;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;

class MockDeckFactory
{
    public static function createDeck(PHPUnit_Framework_TestCase $testCase, CardCollection $board, HandCollection $allHands): MockObject
    {
        $cards = CardCollection::make();

        $allHands
            ->each(function (Hand $hand) use ($cards) {
                $cards->push($hand->cards()->get(0));
            })
            ->each(function (Hand $hand) use ($cards) {
                $cards->push($hand->cards()->get(1));
            })
        ;

        $actualDeck = CardCollection::make((new Deck())->getCards())->diff($cards)->shuffle()->values();

        // burn
        $cards->push($actualDeck->pop());

        // flop
        $board->splice(0, 3)
              ->each(function (Card $card) use ($cards) {
                  $cards->push($card);
              })
        ;

        // burn
        $cards->push($actualDeck->pop());

        // turn
        $cards->push($board->get(0));

        // burn
        $cards->push($actualDeck->pop());

        // river
        $cards->push($board->get(1));
        $newCards = $cards->merge($actualDeck->diff($cards));

        $deck = $testCase->createMock(Deck::class);

        $deck->method('shuffle')
             ->willReturn($newCards->toArray())
        ;

        $cards->each(function (Card $card, $index) use ($deck, $testCase) {
            $deck->expects($testCase->at($index + 1))
                 ->method('draw')
                 ->willReturn($card)
            ;
        });

        return $deck;
    }
}
