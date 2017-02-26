<?php

namespace Cysha\Casino\Holdem\Tests\Exceptions;

use Cysha\Casino\Holdem\Exceptions\GameParametersException;
use PHPUnit_Framework_TestCase as PHPUnit;

class GameParametersExceptionTest extends PHPUnit
{
    /** @test */
    public function invalid_argument_can_accept_custom_messages()
    {
        $expectedException = new GameParametersException('custom message');
        $this->assertEquals($expectedException, GameParametersException::invalidArgument('custom message'));
    }
}
