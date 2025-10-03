<?php

namespace Tests\Unit\Game\Domain\Game;

use DomainException;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Money;

class GamePlayersTurnStageTest extends GameTest
{


    public function test_stage_creates_correctly(): void
    {
        $this->playersTurnsStep();
        $this->assertSame($this->game->state(), GameState::PlayersTurn);
        $this->assertTrue($this->game->currentPlayer()->id()->equals($this->firstGamePlayer()->id()));
        $this->assertSame($this->game->currentPlayer()->state(), PlayerState::Active);
        foreach ($this->game->players() as $player) {
            if ($player->id()->equals($this->game->currentPlayer()->id())) {
                continue;
            }
            $this->assertSame($player->state(), PlayerState::PlacedABet);
        }
    }

    public function test_cant_start_stage_while_all_players_wont_place_a_bet(): void
    {
        $this->betStartStep();
        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    public function test_cant_start_stage_while_all_players_wont_receive_cards(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $this->game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
        }

        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    public function test_cant_start_stage_while_dealer_wont_receive_card(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $this->game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
            $player->hand()->receiveCard($this->game->shoe()->draw());
            $player->hand()->receiveCard($this->game->shoe()->draw());
        }

        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    public function test_other_player_cant_make_turn(): void
    {
        $this->playersTurnsStep();
        $otherPlayer = $this->game->players()[array_key_last($this->game->players())];
        $this->expectException(DomainException::class);
        $this->game->playerStand($otherPlayer->id());
    }

    public function test_current_player_can_make_turn(): void
    {
        $this->playersTurnsStep();
        $firstPlayer = $this->firstGamePlayer();
        $this->game->playerStand($firstPlayer->id());
        $this->assertSame($firstPlayer->state(), PlayerState::Standing);
    }

    public function test_current_player_can_hit(): void
    {
        $this->playersTurnsStep();
        $firstPlayer = $this->firstGamePlayer();
        $this->game->playerHit($firstPlayer->id());
        $this->assertSame(count($this->game->currentPlayer()->hand()->cards()),3);
    }

    public function test_current_player_is_changing_after_his_turn_end_with_hit(): void
    {
        $this->playersTurnsStep();
        for ($i = 0; $i < 10; $i++) {
            $firstPlayer = $this->firstGamePlayer();
            while ($this->game->currentPlayer()->id()->equals($firstPlayer->id())){
                $this->game->playerHit($this->game->currentPlayer()->id());
            }
            $this->assertFalse($firstPlayer->id()->equals($this->game->currentPlayer()->id()));
        }

    }

    public function test_current_player_can_stand_and_next_player_will_be_active(): void
    {
        $this->playersTurnsStep();
        $firstPlayer = $this->firstGamePlayer();
        $this->game->playerStand($firstPlayer->id());
        $this->assertSame($firstPlayer->state(), PlayerState::Standing);
        $this->assertSame($this->game->currentPlayer()->state(), PlayerState::Active);
    }

    public function test_after_last_player_turn_starts_dealer_stage(): void
    {
        $this->playersTurnsStep();
        $this->game->playerStand($this->game->currentPlayer()->id());
        $this->game->playerStand($this->game->currentPlayer()->id());
        $this->game->playerStand($this->game->currentPlayer()->id());
        $this->assertSame($this->game->state(), GameState::DealerTurn);
    }



}
