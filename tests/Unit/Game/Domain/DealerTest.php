<?php

namespace Tests\Unit\Game\Domain;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class DealerTest extends TestCase
{
    public function test_dealer_correctly_deal_initial_cards(): void
    {
        $game = GameFactory::makeTestGameInPlayersTurnStage();
        foreach ($game->players() as $player){
            $this->assertCount(2, $player->hand()->cards());
        }
        $this->assertCount(1, $game->dealerHand()->cards());
    }

    public function test_it_takes_cards_until_it_has_17(): void
    {
        $game = GameFactory::makeTestGameInDealerTurnStage();
        $game->dealerHand()->returnCards();

        $game->dealerHand()->receiveCard(new Card(Rank::ten(), Suit::clubs()));

        for ($i = 0; $i < 100; $i++) {
            $game->placeOtherDealerCards();
            $this->assertTrue($game->dealerScore() >= 17);
        }
    }
}
