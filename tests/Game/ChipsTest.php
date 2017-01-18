<?php

namespace xLink\Tests\Exceptions;

use xLink\Poker\Game\Chips;

class ChipsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_be_created_from_0()
    {
        $chips = Chips::zero();
        $this->assertEquals(0, $chips->amount());
    }

    /** @test */
    public function chips_can_be_added()
    {
        $chips = Chips::zero();
        $chips->add(Chips::fromAmount(100));

        $this->assertEquals($chips->amount(), 100);
    }

    /** @test */
    public function chips_can_be_deducted()
    {
        $chips = Chips::fromAmount(100);
        $chips->subtract(Chips::fromAmount(100));

        $this->assertEquals($chips->amount(), 0);
    }
}
