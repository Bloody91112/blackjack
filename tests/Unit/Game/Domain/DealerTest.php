<?php

namespace Tests\Unit\Game\Domain;

use PHPUnit\Framework\TestCase;
use Tests\Unit\Game\Domain\Game\GameTest;

class DealerTest extends TestCase
{
    public function test_dealer_correctly_deal_initial_cards(): void
    {
        $game = GameTest::makeTestGame();
        GameTest::playersTurnsStep($game);
        foreach ($game->players() as $player){
            $this->assertCount(2, $player->hand()->cards());
        }
        $this->assertCount(1, $game->dealerHand()->cards());
    }
}
