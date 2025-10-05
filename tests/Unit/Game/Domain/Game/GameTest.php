<?php

namespace Tests\Unit\Game\Domain\Game;

use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;

class GameTest extends TestCase
{

    public function test_it_found_player_correctly(): void
    {
        $game = GameFactory::makeTestGame();
        $firstPlayer = $game->players()[array_key_first($game->players())];
        $foundPlayer = $game->findPlayer($firstPlayer->id());
        $this->assertInstanceOf(Player::class, $foundPlayer);
    }

    public function test_it_throws_exception_when_trying_to_find_other_player(): void
    {
        $game = GameFactory::makeTestGame();
        $player = new Player(PlayerId::generate(), "Anna");
        $this->expectException(LogicException::class);
        $game->findPlayer($player->id());
    }

    public function test_standing_players_are_real_standing_players(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $firstPlayer = $game->players()[array_key_first($game->players())];

        while ($game->currentPlayer()->id()->equals($firstPlayer->id())){
            $game->playerHit($game->currentPlayer()->id());
        }

        $secondPlayer = $game->currentPlayer();
        while ($game->currentPlayer()->id()->equals($secondPlayer->id())){
            $game->playerHit($game->currentPlayer()->id());
        }

        $thirdPlayer = $game->currentPlayer();
        $game->playerStand($thirdPlayer->id());

        $this->assertCount(1, $game->standingPlayers());
        $this->assertTrue($game->standingPlayers()[array_key_first($game->standingPlayers())]->id()->equals($thirdPlayer->id()));
    }

    public function test_dealer_can_receive_card(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $this->assertCount(1, $game->dealerHand()->cards());
        $game->placeDealerCard();
        $this->assertCount(2, $game->dealerHand()->cards());
    }

    public function test_it_correctly_returns_dealer_score(): void
    {
        $game = GameFactory::makeTestGame();
        $this->assertIsNumeric($game->dealerScore());
    }

}
