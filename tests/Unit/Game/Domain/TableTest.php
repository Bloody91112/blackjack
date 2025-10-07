<?php

namespace Tests\Unit\Game\Domain;

use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Table;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Ids\TableId;

class TableTest extends TestCase
{

    private Table $table;
    protected function setUp(): void
    {
        parent::setUp();
        $shoe = (new ShoeFactory(new DeckFactory))->create(3);
        $this->table = new Table(TableId::generate(), $shoe);
    }

    public function test_players_can_join_table(): void
    {
        $this->assertCount(0, $this->table->players());
        $this->table->join(new Player(PlayerId::generate(), "John"));
        $this->table->join(new Player(PlayerId::generate(), "Bob"));
        $this->table->join(new Player(PlayerId::generate(), "Alex"));
        $this->assertCount(3, $this->table->players());
    }

    public function test_same_players_cant_join_twice(): void
    {
        $player = new Player(PlayerId::generate(), "John");
        $this->table->join($player);

        $this->expectException(LogicException::class);
        $this->table->join($player);
    }

    public function test_it_start_new_game(): void
    {
        $this->table->join(new Player(PlayerId::generate(), "John"));
        $this->table->join(new Player(PlayerId::generate(), "Bob"));
        $this->table->join(new Player(PlayerId::generate(), "Alex"));
        $this->table->startGame();
        $this->assertSame($this->table->game()->state(), GameState::Betting);
        $this->assertCount(3, $this->table->game()->players());
    }
}
