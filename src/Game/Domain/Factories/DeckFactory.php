<?php

namespace Src\Game\Domain\Factories;

use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class DeckFactory
{
    public static function createStandardDeck(): Deck
    {
        $cards = [];
        foreach (Suit::all() as $suit) {
            foreach (Rank::all() as $rank) {
                $cards[] = new Card($rank, $suit);
            }
        }
        return Deck::from($cards);
    }
}
