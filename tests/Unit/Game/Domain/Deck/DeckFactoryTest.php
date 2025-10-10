<?php

namespace Tests\Unit\Game\Domain\Deck;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class DeckFactoryTest extends TestCase
{
    private DeckFactory $deckFactory;

    protected function setUp(): void
    {
        $this->deckFactory = new DeckFactory();
    }

    public function test_it_correctly_creates_standard_deck(): void
    {
        $deck = DeckFactory::createStandardDeck();
        $this->assertSame(count($deck->cards()), 52);
    }

    public function test_it_has_unique_cards(): void
    {
        $deck = DeckFactory::createStandardDeck();

        $hashes = [];
        foreach ($deck->cards() as $card) {
            $hash = $card->suit()->value() . '_' . $card->rank()->value();
            $this->assertArrayNotHasKey($hash, $hashes, "Duplicate card found: $hash");
            $hashes[$hash] = true;
        }

        foreach (Suit::all() as $suit) {
            foreach (Rank::all() as $rank) {
                $key = $suit->value() . '_' . $rank->value();
                $this->assertArrayHasKey($key, $hashes, "Missing card: $key");
            }
        }
    }


}
