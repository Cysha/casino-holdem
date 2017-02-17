<?php

namespace Cysha\Casino\Holdem\Tests\Exceptions;

use PHPUnit_Framework_TestCase;
use Cysha\Casino\Holdem\Exceptions\RoundException;

class RoundExceptionTest extends PHPUnit_Framework_TestCase
{
    public function test_the_flop_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::flopHasBeenDealt('custom message'));
    }

    public function test_the_turn_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::turnHasBeenDealt('custom message'));
    }

    public function test_the_river_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::riverHasBeenDealt('custom message'));
    }
}
