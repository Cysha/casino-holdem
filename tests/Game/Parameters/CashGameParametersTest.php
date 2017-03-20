<?php

namespace Cysha\Casino\Holdem\Tests\Game\Parameters;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Parameters\CashGameParameters;
use PHPUnit_Framework_TestCase as PHPUnit;
use Ramsey\Uuid\Uuid;

class CashGameParametersTest extends PHPUnit
{
    public function setUp()
    {
    }

    /** @test */
    public function can_spawn_default_ruleset()
    {
        $ruleset = new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(50), null, 9, Chips::fromAmount(50));

        $this->assertInstanceOf(CashGameParameters::class, $ruleset);
        $this->assertEquals(50, $ruleset->bigBlind()->amount());
        $this->assertEquals(25, $ruleset->smallBlind()->amount());
        $this->assertEquals(9, $ruleset->tableSize());
        $this->assertEquals(50, $ruleset->minimumBuyIn()->amount());
        $this->assertEquals(0, $ruleset->maximumBuyIn()->amount());
    }

    /** @test */
    public function can_set_blinds()
    {
        $ruleset = new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(1), 2, Chips::fromAmount(50));

        $this->assertInstanceOf(CashGameParameters::class, $ruleset);
        $this->assertEquals(1, $ruleset->bigBlind()->amount());
        $this->assertEquals(1, $ruleset->smallBlind()->amount());
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\GameParametersException
     * @test
     */
    public function minimum_table_size_is_2()
    {
        new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(1), 1, Chips::fromAmount(50));
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\GameParametersException
     * @test
     */
    public function big_blind_cant_be_smaller_than_small_blind()
    {
        new CashGameParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(2), 2, Chips::fromAmount(50));
    }
}
