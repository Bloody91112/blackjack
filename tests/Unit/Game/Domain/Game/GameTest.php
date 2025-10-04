<?php

namespace Tests\Unit\Game\Domain\Game;

use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class GameTest extends TestCase
{

    protected Game $game;
    protected Dealer $dealer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = self::makeTestGame();
        $this->dealer = new Dealer();
    }

    public static function makeTestGame(): Game
    {
        $shoe = (new ShoeFactory(new DeckFactory))->create(3);
        $players = [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
            new Player(PlayerId::generate(), "Alex"),
        ];

        $game = (new GameFactory)->create($players, $shoe);

        foreach ($game->players() as $player){
            $player->joinTable();
        }

        return $game;
    }

    public function test_it_found_player_correctly(): void
    {
        $firstPlayer = $this->game->players()[array_key_first($this->game->players())];
        $foundPlayer = $this->game->findPlayer($firstPlayer->id());
        $this->assertInstanceOf(Player::class, $foundPlayer);
    }

    public function test_it_throws_exception_when_trying_to_find_other_player(): void
    {
        $player = new Player(PlayerId::generate(), "Anna");
        $this->expectException(LogicException::class);
        $this->game->findPlayer($player->id());
    }

    public function test_standing_players_are_real_standing_players(): void
    {
        self::playersTurnsStep($this->game);
        $firstPlayer = $this->firstGamePlayer();
        while ($this->game->currentPlayer()->id()->equals($firstPlayer->id())){
            $this->game->playerHit($this->game->currentPlayer()->id());
        }

        $secondPlayer = $this->game->currentPlayer();
        while ($this->game->currentPlayer()->id()->equals($secondPlayer->id())){
            $this->game->playerHit($this->game->currentPlayer()->id());
        }

        $thirdPlayer = $this->game->currentPlayer();
        $this->game->playerStand($thirdPlayer->id());

        $this->assertCount(1, $this->game->standingPlayers());
        $this->assertTrue($this->game->standingPlayers()[array_key_first($this->game->standingPlayers())]->id()->equals($thirdPlayer->id()));
    }

    public function test_dealer_can_receive_card(): void
    {
        self::dealerTurnStep($this->game);
        $this->assertCount(1, $this->game->dealerHand()->cards());
        $this->game->placeDealerCard();
        $this->assertCount(2, $this->game->dealerHand()->cards());
    }

    public function test_it_correctly_returns_dealer_score(): void
    {
        $this->assertIsNumeric($this->game->dealerScore());
    }





    public static function betStartStep(Game $game): void
    {
        $game->betStart();
    }

    public static function playersTurnsStep(Game $game): void
    {
        self::betStartStep($game);
        foreach ($game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $game->placeBet($player->id(), $bet);
        }
        $dealer = new Dealer();
        $dealer->dealInitialCards($game);
        $game->playersTurnsStage();
    }

    public static function dealerTurnStep(Game $game): void
    {
        self::playersTurnsStep($game);

        $firstGamePlayer = $game->players()[array_key_first($game->players())];
        while ($game->currentPlayer()->id()->equals($firstGamePlayer->id())){
            $game->playerHit($game->currentPlayer()->id());
        }

        $game->playerStand($game->currentPlayer()->id());
        $game->playerStand($game->currentPlayer()->id());
    }

    protected function firstGamePlayer(): Player
    {
        return $this->game->players()[array_key_first($this->game->players())];
    }

}
