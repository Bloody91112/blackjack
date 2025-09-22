<?php

namespace Tests\Unit\Game\Domain\Shoe;

use DomainException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class DeckTest extends TestCase
{
    private Deck $deck;
    private array $cards;

    protected function setUp(): void
    {
        parent::setUp();
        $cards = [];

        foreach (Suit::all() as $suit){
            foreach (Rank::all() as $rank){
                $cards[] = new Card($rank, $suit);
            }
        }

        $this->cards = $cards;
        $this->deck = Deck::from($cards);
    }

    public function test_it_throws_exception_on_incorrect_cards_number(): void
    {
        array_pop($this->cards);
        $this->expectException(DomainException::class);
        Deck::from($this->cards);
    }

    public function test_it_throws_exception_on_cards_duplicate(): void
    {
        array_pop($this->cards);
        $this->cards[] = $this->cards[0];
        $this->expectException(DomainException::class);
        Deck::from($this->cards);
    }

    public function test_equal_decks_are_equal(): void
    {
        $deck = (new DeckFactory())->createStandardDeck();
        $this->assertTrue($deck->equalsTo($this->deck));
    }

    public function test_it_return_cards(): void
    {
        $cards = $this->deck->cards();
        $this->assertSame(count($cards), 52);
    }
}
