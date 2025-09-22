<?php

namespace Tests\Unit\Game\Domain\Shoe;

use DomainException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class ShoeFactoryTest extends TestCase
{

    private ShoeFactory $shoeFactory;
    protected function setUp(): void
    {
        $this->shoeFactory = new ShoeFactory(new DeckFactory());
    }

    public function test_it_creates_with_correct_cards_number(): void
    {
        $shoe = $this->shoeFactory->create(2);
        $this->assertCount(2 * Deck::STANDARD_SIZE, $shoe->cards());
    }

    public function test_it_throws_exceptions_on_incorrect_decks_number(): void
    {
        $this->expectException(DomainException::class);
        $this->shoeFactory->create(10);

        $this->expectException(DomainException::class);
        $this->shoeFactory->create(0);
    }



}
