<?php

namespace Cysha\Casino\Holdem\Tests\Game\Parameters;

use Cysha\Casino\Game\Chips;
use Cysha\Casino\Holdem\Game\Parameters\DefaultParameters;
use PHPUnit_Framework_TestCase as PHPUnit;
use Ramsey\Uuid\Uuid;

class DefaultParametersTest extends PHPUnit
{
    public function setUp()
    {
    }

    /** @test */
    public function can_spawn_default_ruleset()
    {
        $id = Uuid::uuid4();
        $ruleset = new DefaultParameters($id, Chips::fromAmount(50), null, 9);

        $this->assertInstanceOf(DefaultParameters::class, $ruleset);
        $this->assertEquals($id, $ruleset->gameId());
        $this->assertEquals(50, $ruleset->bigBlind()->amount());
        $this->assertEquals(25, $ruleset->smallBlind()->amount());
        $this->assertEquals(9, $ruleset->tableSize());
    }

    /** @test */
    public function can_set_blinds()
    {
        $ruleset = new DefaultParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(1), 2);

        $this->assertInstanceOf(DefaultParameters::class, $ruleset);
        $this->assertEquals(1, $ruleset->bigBlind()->amount());
        $this->assertEquals(1, $ruleset->smallBlind()->amount());
    }

    /** @test */
    public function given_small_blind_being_zero_or_null_make_it_half_big_blind()
    {
        $ruleset = new DefaultParameters(Uuid::uuid4(), Chips::fromAmount(20), Chips::fromAmount(0), 2);

        $this->assertInstanceOf(DefaultParameters::class, $ruleset);
        $this->assertEquals(20, $ruleset->bigBlind()->amount());
        $this->assertEquals(10, $ruleset->smallBlind()->amount());
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\GameParametersException
     * @test
     */
    public function minimum_table_size_is_2()
    {
        new DefaultParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(1), 1);
    }

    /**
     * @expectedException Cysha\Casino\Holdem\Exceptions\GameParametersException
     * @test
     */
    public function big_blind_cant_be_smaller_than_small_blind()
    {
        new DefaultParameters(Uuid::uuid4(), Chips::fromAmount(1), Chips::fromAmount(2), 1);
    }
}
