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
        $rank = Rank::from(Rank::FIVE);
        $suit = Suit::from(Suit::CLUBS);
        $firstCard = new Card($rank, $suit);
        $secondCard = new Card($rank, $suit);

        $this->assertTrue($firstCard->equalsTo($secondCard));
    }

    public function test_two_different_cards_are_not_equal(): void
    {
        $firstCard = new Card(
            Rank::from(Rank::SIX),
            Suit::from(Suit::CLUBS)
        );

        $secondCard = new Card(
            Rank::from(Rank::EIGHT),
            Suit::from(Suit::HEARTS)
        );

        $this->assertFalse($firstCard->equalsTo($secondCard));
    }
}
