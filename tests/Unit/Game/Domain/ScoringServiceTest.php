<?php

namespace Tests\Unit\Game\Domain;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Game;
use Tests\Unit\Game\Domain\Game\GameTest;

class ScoringServiceTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();
        $game = GameTest::makeTestGame();
        GameTest::dealerTurnStep($game);
        $this->game = $game;
    }
}
