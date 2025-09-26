<?php

namespace Tests\Unit\Game\Domain\Shoe;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;

class ShoeTest extends TestCase
{

    public const DECKS = 7;

    private Shoe $shoe;
    protected function setUp(): void
    {
        parent::setUp();

        $shoeFactory = new ShoeFactory(new DeckFactory());
        $this->shoe = $shoeFactory->create(self::DECKS);
    }

    public function test_it_correctly_creates(): void
    {
        $this->assertSame(count($this->shoe->cards()), self::DECKS * Deck::STANDARD_SIZE);
        $this->assertSame(count($this->shoe->decks()), self::DECKS);
    }

    public function test_it_shuffles_correctly(): void
    {
        $firstKey = array_key_first($this->shoe->cards());
        $lastKey = array_key_last($this->shoe->cards());

        $this->shoe->shuffle();

        $this->assertNotSame($firstKey, array_key_first($this->shoe->cards()));
        $this->assertNotSame($lastKey, array_key_last($this->shoe->cards()));
    }

    public function test_it_draws_card_correctly(): void
    {
        $drawCardKey = array_key_first($this->shoe->cards());
        $this->assertTrue(isset($this->shoe->cards()[$drawCardKey]));

        $card = $this->shoe->draw();
        $shoeCards = $this->shoe->cards();
        $this->assertFalse(isset($shoeCards[$drawCardKey]));
        $this->assertTrue(count($this->shoe->cards()) === (self::DECKS * Deck::STANDARD_SIZE) - 1);
        $this->assertInstanceOf(Card::class, $card);
    }

    public function test_it_collects_a_card_correctly(): void
    {
        $card = $this->shoe->draw();
        $this->shoe->draw();

        $this->shoe->collect($card);

        $this->assertSame(count($this->shoe->cards()), (self::DECKS * Deck::STANDARD_SIZE) - 1);
    }
}
