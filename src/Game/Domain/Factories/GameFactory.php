<?php

namespace Src\Game\Domain\Factories;

use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class GameFactory
{
    public function __construct()
    {
    }

    public function create(array $players, Shoe $shoe): Game
    {
        $shoe->shuffle();
        $dealerHand = new Hand(HandId::generate(), new HandValue());
        return new Game(GameId::generate(), $shoe, $dealerHand, $players);
    }

    public static function makeTestGame(): Game
    {
        $shoe = (new ShoeFactory(new DeckFactory))->create(3);
        $players = [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
            new Player(PlayerId::generate(), "Alex"),
            new Player(PlayerId::generate(), "Michael"),
            new Player(PlayerId::generate(), "Anna"),
            new Player(PlayerId::generate(), "Alice"),
            new Player(PlayerId::generate(), "Victoria"),
        ];

        $game = (new GameFactory)->create($players, $shoe);

        foreach ($game->players() as $player){
            $player->joinTable();
        }

        return $game;
    }

    public static function makeTestGameInBetStage(): Game
    {
        $game = self::makeTestGame();
        $game->betStart();
        return $game;
    }

    public static function makeTestGameInPlayersTurnStage(): Game
    {
        $game = self::makeTestGameInBetStage();
        foreach ($game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $game->placeBet($player->id(), $bet);
        }
        $dealer = new Dealer();
        $dealer->dealInitialCards($game);
        $game->playersTurnsStage();
        return $game;
    }

    public static function makeTestGameInDealerTurnStage(): Game
    {
        $game = self::makeTestGameInPlayersTurnStage();

        foreach ($game->players() as $player){
            if (!$player->hand()->hasBlackjack()){
                $game->playerStand($player->id());
            }
        }

        return $game;
    }
}
