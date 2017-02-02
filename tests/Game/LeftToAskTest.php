<?php

namespace xLink\Tests\Game;

use xLink\Poker\Game\Chips;

class LeftToActTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_be_created_from_0()
    {
        $chips = Chips::zero();
        $this->assertEquals(0, $chips->amount());
    }
}
