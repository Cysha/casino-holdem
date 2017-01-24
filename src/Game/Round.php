<?php

namespace xLink\Poker\Game;

use xLink\Poker\Table;

class Round
{
    /**
     * @var Table
     */
    private $table;

    /**
     * Round constructor.
     *
     * @param Table $table
     */
    private function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @param Table $table
     *
     * @return Round
     */
    public static function start(Table $table): Round
    {
        return new static($table);
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->table->players();
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return $this->table;
    }

    /**
     * @return Player
     */
    public function playerWithButton(): Player
    {
        return $this->table()->locatePlayerWithButton();
    }

    /**
     * @return Player
     */
    public function playerWithSmallBlind(): Player
    {
        if ($this->table()->playersSatDown()->count() === 2) {
            return $this->table()->playersSatDown()->get(0);
        }

        return $this->table()->playersSatDown()->get(1);
    }

    /**
     * @return Player
     */
    public function playerWithBigBlind(): Player
    {
        if ($this->table()->playersSatDown()->count() === 2) {
            return $this->table()->playersSatDown()->get(1);
        }

        return $this->table()->playersSatDown()->get(2);
    }
}
