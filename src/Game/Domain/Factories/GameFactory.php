<?php

namespace Src\Game\Domain\Factories;

use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\Services\ScoringService;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class GameFactory
{

    public const DECKS_IN_SHOE_IN_TEST_GAME = 3;

    public function __construct()
    {
    }

    public function create(array $players, Shoe $shoe): Game
    {
        $shoe->shuffle();
        $dealerHand = new Hand(HandId::generate());
        return new Game(GameId::generate(), $shoe, $dealerHand, $players);
    }

    public static function makeTestGame(): Game
    {
        $game = new Game(
            GameId::generate(),
            (new ShoeFactory(new DeckFactory))->create(self::DECKS_IN_SHOE_IN_TEST_GAME),
            new Hand(HandId::generate()),
            [
                new Player(PlayerId::generate(), "John"),
                new Player(PlayerId::generate(), "Bob"),
                new Player(PlayerId::generate(), "Alex"),
            ]
        );

        foreach ($game->players() as $player){
            $player->joinTable();
        }

        return $game;
    }

    public static function makeTestGameInBetStage(Game $game = null): Game
    {
        $game = $game ?? self::makeTestGame();
        $game->betStart();
        return $game;
    }

    public static function makeTestGameInPlayersTurnStage(Game $game = null): Game
    {
        $game = self::makeTestGameInBetStage($game);
        foreach ($game->players() as $player) {
            $bet = new Bet(BetId::generate(), new Money(100));
            $game->placeBet($player->id(), $bet);
        }
        $dealer = new Dealer();
        $dealer->dealInitialCards($game);
        $game->playersTurnsStage();
        return $game;
    }

    public static function makeTestGameInDealerTurnStage(Game $game = null): Game
    {
        $game = self::makeTestGameInPlayersTurnStage($game);

        foreach ($game->players() as $player){
            if (!$player->hand()->hasBlackjack()){
                $game->playerStand($player->id());
            }
        }

        return $game;
    }

    public static function makeTestGameInFinishStage(Game $game = null): Game
    {
        $game = self::makeTestGameInDealerTurnStage($game);
        $game->placeOtherDealerCards();
        (new ScoringService())->calculateResult($game);
        $game->finish();
        return $game;
    }
}
