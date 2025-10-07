<?php

namespace Tests\Unit\Game\Domain\Game;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\Deck;


class GameFinishStageTest extends TestCase
{


    public function test_game_ended_with_correct_status(): void
    {
        $game = GameFactory::makeTestGameInFinishStage();
        $this->assertSame($game->state(), GameState::Finished);
    }

    public function test_all_cards_returns_to_shoe_after_game_end(): void
    {
        $game = GameFactory::makeTestGameInFinishStage();
        foreach ($game->players() as $player){
            $this->assertCount(0, $player->hand()->cards());
        }

        $this->assertCount(0, $game->dealerHand()->cards());

        $cardsInShoeTotal = Deck::STANDARD_SIZE * GameFactory::DECKS_IN_SHOE_IN_TEST_GAME;
        $this->assertCount($cardsInShoeTotal, $game->shoe()->cards());
    }

    public function test_all_players_has_correct_status_after_game_end(): void
    {
        $game = GameFactory::makeTestGameInFinishStage();
        foreach ($game->players() as $player){
            $this->assertSame($player->state(), PlayerState::Finished);
        }
    }


}
