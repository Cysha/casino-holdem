<?php

namespace Cysha\Casino\Holdem\Cards;

use Cysha\Casino\Cards\ResultCollection;

class SevenCardResultCollection extends ResultCollection
{
    public function __toString()
    {
        return $this->map->definition();
    }
}
