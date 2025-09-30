<?php

namespace Src\Game\Domain\Services;


use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\HandId;

class Dealer
{

    public function dealInitialCards(Game $game): void
    {
        foreach ($game->players() as $player){
            $player->assignHand(new Hand(HandId::generate(), new HandValue()));

            $player->hand()->receiveCard($game->shoe()->draw());

            $player->hand()->receiveCard($game->shoe()->draw());
        }

        $game->dealerHand()->receiveCard($game->shoe()->draw());
    }

}
