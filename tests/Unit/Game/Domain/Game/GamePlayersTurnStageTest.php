<?php

namespace Tests\Unit\Game\Domain\Game;

use DomainException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Money;

class GamePlayersTurnStageTest extends TestCase
{


    public function test_stage_creates_correctly(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $this->assertSame($game->state(), GameState::PlayersTurn);
        $firstPlayer = $game->players()[array_key_first($game->players())];
        $this->assertTrue($game->currentPlayer()->id()->equals($firstPlayer->id()));
        $this->assertSame($game->currentPlayer()->state(), PlayerState::Active);
        foreach ($game->players() as $player) {
            if ($player->id()->equals($game->currentPlayer()->id())) {
                continue;
            }
            $this->assertSame($player->state(), PlayerState::PlacedABet);
        }
    }

    public function test_cant_start_stage_while_all_players_wont_place_a_bet(): void
    {
        $game = GameFactory::makeTestGameInBetStage();
        $this->expectException(DomainException::class);
        $game->playersTurnsStage();
    }

    public function test_cant_start_stage_while_all_players_wont_receive_cards(): void
    {
        $game = GameFactory::makeTestGameInBetStage();
        foreach ($game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
        }

        $this->expectException(DomainException::class);
        $game->playersTurnsStage();
    }

    public function test_cant_start_stage_while_dealer_wont_receive_card(): void
    {
        $game = GameFactory::makeTestGameInBetStage();
        foreach ($game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
            $player->hand()->receiveCard($game->shoe()->draw());
            $player->hand()->receiveCard($game->shoe()->draw());
        }

        $this->expectException(DomainException::class);
        $game->playersTurnsStage();
    }

    public function test_other_player_cant_make_turn(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $otherPlayer = $game->players()[array_key_last($game->players())];
        $this->expectException(DomainException::class);
        $game->playerStand($otherPlayer->id());
    }

    public function test_current_player_can_make_turn(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $firstPlayer = $game->players()[array_key_first($game->players())];
        $game->playerStand($firstPlayer->id());
        $this->assertSame($firstPlayer->state(), PlayerState::Standing);
    }

    public function test_current_player_can_hit(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $firstPlayer = $game->players()[array_key_first($game->players())];
        $game->playerHit($firstPlayer->id());
        $this->assertSame(count($firstPlayer->hand()->cards()),3);
    }

    public function test_current_player_is_changing_after_his_turn_end_with_hit(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        for ($i = 0; $i < 10; $i++) {
            $firstPlayer = $game->players()[array_key_first($game->players())];
            while ($game->currentPlayer()->id()->equals($firstPlayer->id())){
                $game->playerHit($game->currentPlayer()->id());
            }
            $this->assertFalse($firstPlayer->id()->equals($game->currentPlayer()->id()));
        }

    }

    public function test_current_player_can_stand_and_next_player_will_become_active(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        $firstPlayer = $game->players()[array_key_first($game->players())];
        $game->playerStand($firstPlayer->id());
        $this->assertSame($firstPlayer->state(), PlayerState::Standing);
        $this->assertSame($game->currentPlayer()->state(), PlayerState::Active);
    }

    public function test_after_last_player_turn_starts_dealer_stage(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $this->assertSame($game->state(), GameState::DealerTurn);
    }

    public function test_players_and_dealer_are_receiving_cards(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        foreach ($game->players() as $player){
            $this->assertCount(2, $player->hand()->cards());
        }
        $this->assertCount(1, $game->dealerHand()->cards());
    }

}
