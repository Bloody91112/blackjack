<?php

namespace Tests\Unit\Game\Domain\Game;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Money;

class GameBetStageTest extends TestCase
{


    public function test_bet_stage_starts_correctly(): void
    {
        $game = GameFactory::makeTestGameInBetStage();

        foreach ($game->players() as $player){
            $this->assertSame($player->state(), PlayerState::ChoosingABet);
        }
        $this->assertSame($game->state(), GameState::Betting);
    }

    public function test_it_places_player_bet_correctly(): void
    {
        $game = GameFactory::makeTestGameInBetStage();
        $bet = new Bet(BetId::generate(), new Money(100));
        $player = $game->players()[array_key_first($game->players())];
        $game->placeBet($player->id(), $bet);
        $this->assertSame($player->state(), PlayerState::PlacedABet);
    }

}
