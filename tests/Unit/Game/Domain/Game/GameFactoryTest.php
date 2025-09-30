<?php

namespace Tests\Unit\Game\Domain\Game;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;

class GameFactoryTest extends TestCase
{

    public function test_it_creates_a_correct_game(): void
    {
        $shoe = (new ShoeFactory(new DeckFactory))->create(3);
        $players = [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
        ];

        $game = (new GameFactory)->create($players, $shoe);
        $this->assertSame($game->state(), GameState::Created);
        $this->assertCount(2, $game->players());
    }
}
