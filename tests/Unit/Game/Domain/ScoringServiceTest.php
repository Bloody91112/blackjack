<?php

namespace Tests\Unit\Game\Domain;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Enum\PlayerResult;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Services\ScoringService;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class ScoringServiceTest extends TestCase
{
    public function test_it_calculates_result_correct_when_dealer_has_less_than_21(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $game->dealerHand()->returnCards();
        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::five(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::three(), Suit::clubs()));

        $players = $game->standingPlayers();
        (new ScoringService)->calculateResult($game);

        foreach ($players as $player){
            if ($player->hand()->value()->score() < $game->dealerScore()){
                $this->assertSame($player->result(), PlayerResult::Lost);
            } elseif ($player->hand()->value()->score() === $game->dealerScore()){
                $this->assertSame($player->result(), PlayerResult::Push);
            } else {
                $this->assertSame($player->result(), PlayerResult::Won);
            }
        }
    }

    public function test_it_calculates_result_correct_when_dealer_has_blackjack(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $game->dealerHand()->returnCards();
        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::ace(), Suit::clubs()));

        $players = $game->standingPlayers();
        (new ScoringService)->calculateResult($game);

        foreach ($players as $player){
            $this->assertSame($player->result(), PlayerResult::Lost);
        }
    }

    public function test_it_calculates_result_correct_when_dealer_lose(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $game->dealerHand()->returnCards();

        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));
        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));

        $players = $game->standingPlayers();
        (new ScoringService)->calculateResult($game);

        foreach ($players as $player){
            $this->assertSame($player->result(), PlayerResult::Won);
        }
    }
}
