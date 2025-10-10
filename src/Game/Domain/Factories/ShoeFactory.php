<?php

namespace Src\Game\Domain\Factories;

use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\ValueObjects\Ids\ShoeId;

class ShoeFactory
{
    public function __construct(
        private DeckFactory $deckFactory
    ){}

    public function create(int $numberOfDecks): Shoe
    {
        $decks = [];
        for ($i = 0; $i < $numberOfDecks; $i++) {
            $decks[] = DeckFactory::createStandardDeck();
        }
        return new Shoe(ShoeId::generate(), $decks);
    }
}
