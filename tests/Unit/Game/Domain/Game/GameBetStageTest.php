<?php

namespace Tests\Unit\Game\Domain\Game;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class GameBetStageTest extends TestCase
{

    private Game $game;
    private Dealer $dealer;

    protected function setUp(): void
    {
        parent::setUp();

        $shoe = (new ShoeFactory(new DeckFactory))->create(3);
        $players = [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
            new Player(PlayerId::generate(), "Alex"),
        ];

        $this->game = (new GameFactory)->create($players, $shoe);

        foreach ($this->game->players() as $player){
            $player->joinTable();
        }

        $this->dealer = new Dealer();
    }

    public function test_bet_stage_starts_correctly(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player){
            $this->assertSame($player->state(), PlayerState::ChoosingABet);
        }
        $this->assertSame($this->game->state(), GameState::Betting);
    }

    public function test_it_places_player_bet_correctly(): void
    {
        $this->betStartStep();
        $bet = new Bet(BetId::generate(), new Money(100));
        $player = $this->firstGamePlayer();
        $this->game->placeBet($player->id(), $bet);
        $this->assertSame($player->state(), PlayerState::PlacedABet);
    }

    private function firstGamePlayer(): Player
    {
        return $this->game->players()[array_key_first($this->game->players())];
    }

    private function betStartStep(): void
    {
        foreach ($this->game->players() as $player){
            $player->joinTable();
        }
        $this->game->betStart();

    }

}
