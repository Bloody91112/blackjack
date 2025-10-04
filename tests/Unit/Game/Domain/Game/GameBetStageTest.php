<?php

namespace Tests\Unit\Game\Domain\Game;

use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Money;

class GameBetStageTest extends GameTest
{


    public function test_bet_stage_starts_correctly(): void
    {
        self::betStartStep($this->game);
        foreach ($this->game->players() as $player){
            $this->assertSame($player->state(), PlayerState::ChoosingABet);
        }
        $this->assertSame($this->game->state(), GameState::Betting);
    }

    public function test_it_places_player_bet_correctly(): void
    {
        self::betStartStep($this->game);
        $bet = new Bet(BetId::generate(), new Money(100));
        $player = $this->firstGamePlayer();
        $this->game->placeBet($player->id(), $bet);
        $this->assertSame($player->state(), PlayerState::PlacedABet);
    }

}
