<?php

namespace Cysha\Casino\Holdem\Tests\Exceptions;

use PHPUnit_Framework_TestCase;
use Cysha\Casino\Holdem\Exceptions\RoundException;

class RoundExceptionTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function the_flop_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::flopHasBeenDealt('custom message'));
    }

    /** @test */
    public function the_turn_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::turnHasBeenDealt('custom message'));
    }

    /** @test */
    public function the_river_has_been_dealt_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::riverHasBeenDealt('custom message'));
    }

    /** @test */
    public function the_invalid_button_position_can_accept_custom_messages()
    {
        $expectedException = new RoundException('custom message');
        $this->assertEquals($expectedException, RoundException::invalidButtonPosition('custom message'));
    }
}
