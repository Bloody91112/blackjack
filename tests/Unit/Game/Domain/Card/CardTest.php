<?php

namespace Tests\Unit\Game\Domain\Card;


use PHPUnit\Framework\TestCase;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;


class CardTest extends TestCase
{

    public function test_two_same_cards_are_equal(): void
    {
        $firstCard = new Card(Rank::five(), Suit::clubs());
        $secondCard = new Card(Rank::five(), Suit::clubs());

        $this->assertTrue($firstCard->equalsTo($secondCard));
    }

    public function test_two_different_cards_are_not_equal(): void
    {
        $firstCard = new Card(Rank::six(), Suit::clubs());
        $secondCard = new Card(Rank::eight(), Suit::hearts());

        $this->assertFalse($firstCard->equalsTo($secondCard));
    }

    public function test_it_returns_suit(): void
    {
        $card = new Card(Rank::six(), Suit::clubs());
        $this->assertInstanceOf(Suit::class, $card->suit());
    }

    public function test_it_returns_rank(): void
    {
        $card = new Card(Rank::six(), Suit::clubs());
        $this->assertInstanceOf(Rank::class, $card->rank());
    }

    public function test_it_creates_correct_hash_key(): void
    {
        $card = new Card(Rank::six(), Suit::clubs());
        $this->assertSame($card->hashKey(),  $card->suit()->value() . '_' . $card->rank()->value());
    }
}
