<?php

namespace Tests\Unit\Game\Domain\Game;

use DomainException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class GamePlayersTurnStageTest extends TestCase
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

    public function test_players_turn_stage_creates_correctly(): void
    {
        $this->playersTurnsStep();
        $this->assertSame($this->game->state(), GameState::PlayersTurn);
        $this->assertTrue($this->game->currentPlayer()->id()->equals($this->firstGamePlayer()->id()));
        $this->assertSame($this->game->currentPlayer()->state(), PlayerState::Active);
        foreach ($this->game->players() as $player){
            if ($player->id()->equals($this->game->currentPlayer()->id())){
                continue;
            }
            $this->assertSame($player->state(), PlayerState::PlacedABet);
        }
    }

    public function test_cant_start_players_turns_while_all_players_wont_place_a_bet(): void
    {
        $this->betStartStep();
        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    public function test_cant_start_players_turns_while_all_players_wont_receive_cards(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player){
            $bet = new Bet(BetId::generate(), new Money(100));
            $this->game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
        }

        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    public function test_cant_start_players_turns_while_dealer_wont_receive_cards(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player){
            $bet = new Bet(BetId::generate(), new Money(100));
            $this->game->placeBet($player->id(), $bet);
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));
            $player->hand()->receiveCard($this->game->shoe()->draw());
            $player->hand()->receiveCard($this->game->shoe()->draw());
        }

        $this->expectException(DomainException::class);
        $this->game->playersTurnsStage();
    }

    private function betStartStep(): void
    {
        foreach ($this->game->players() as $player){
            $player->joinTable();
        }
        $this->game->betStart();

    }

    private function playersTurnsStep(): void
    {
        $this->betStartStep();
        foreach ($this->game->players() as $player){
            $bet = new Bet(BetId::generate(), new Money(100));
            $this->game->placeBet($player->id(), $bet);
        }
        $dealer = new Dealer();
        $dealer->dealInitialCards($this->game);
        $this->game->playersTurnsStage();
    }

    private function firstGamePlayer(): Player
    {
        return $this->game->players()[array_key_first($this->game->players())];
    }

}
