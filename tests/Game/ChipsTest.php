<?php

namespace Cysha\Casino\Holdem\Tests\Game;

use Cysha\Casino\Holdem\Game\Chips;

class ChipsTest extends BaseGameTestCase
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

        $this->assertEquals(100, $chips->amount());
    }

    /** @test */
    public function chips_can_be_deducted()
    {
        $chips = Chips::fromAmount(100);
        $chips->subtract(Chips::fromAmount(100));

        $this->assertEquals(0, $chips->amount());
    }

    /** @test */
    public function chip_count_is_returned_from_object()
    {
        $chips = Chips::fromAmount(100);

        $this->assertEquals('100', $chips);
    }
}
