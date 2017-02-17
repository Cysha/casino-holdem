<?php

namespace Cysha\Casino\Holdem\Tests\Exceptions;

use PHPUnit_Framework_TestCase;
use Cysha\Casino\Holdem\Exceptions\CardException;

class CardExceptionTest extends PHPUnit_Framework_TestCase
{
    public function test_the_unexpected_suit_has_default_message()
    {
        $expectedException = new CardException('Suit was not a reconigsed value, suit should be heart, club, diamond or spade');
        $this->assertEquals($expectedException, CardException::unexpectedSuit());
    }

    public function test_the_unexpected_suit_can_accept_custom_messages()
    {
        $expectedException = new CardException('custom message');
        $this->assertEquals($expectedException, CardException::unexpectedSuit('custom message'));
    }

    public function test_the_no_value_card_groups_has_default_message()
    {
        $expectedException = new CardException('Tried to find 2 cards with the same value, but failed');
        $this->assertEquals($expectedException, CardException::noCardValueGroupsFound(2));
    }

    public function test_the_no_value_card_groups_can_accept_custom_messages()
    {
        $expectedException = new CardException('custom message');
        $this->assertEquals($expectedException, CardException::noCardValueGroupsFound(2, 'custom message'));
    }
}
