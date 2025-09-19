<?php

namespace Game\Domain\Hand;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class HandValueTest extends TestCase
{
    public function test_it_accepts_only_cards(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $cards = [
            new Card(Rank::from(Rank::JACK), Suit::from(Suit::HEARTS)),
            Suit::from(Suit::CLUBS)
        ];
        new HandValue($cards);
    }

    public function test_it_count_correct_score_with_one_card(): void
    {
        $handValue = new HandValue([
            new Card(Rank::from(Rank::JACK), Suit::from(Suit::HEARTS)),
        ]);
        $this->assertSame($handValue->score(), 10);
    }

    public function test_it_count_correct_score_with_two_cards(): void
    {
        $handValue = new HandValue([
            new Card(Rank::from(Rank::JACK), Suit::from(Suit::HEARTS)),
            new Card(Rank::from(Rank::QUEEN), Suit::from(Suit::HEARTS)),
        ]);

        $this->assertSame($handValue->score(), 20);
    }

    public function test_it_recount_correct(): void
    {
        $handValue = new HandValue([
            new Card(Rank::from(Rank::FOUR), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::THREE), Suit::from(Suit::SPADES)),
        ]);

        $additionalCard = new Card(Rank::from(Rank::SEVEN), Suit::from(Suit::DIAMONDS));

        $handValueAfterRecount = $handValue->recount($additionalCard);

        $this->assertSame($handValueAfterRecount->score(), 14);
    }

    public function test_it_calculate_blackjack_correct(): void
    {
        $withBlackjack = new HandValue([
                new Card(Rank::from(Rank::FOUR), Suit::from(Suit::CLUBS)),
                new Card(Rank::from(Rank::JACK), Suit::from(Suit::SPADES)),
                new Card(Rank::from(Rank::SEVEN), Suit::from(Suit::SPADES)),
        ]);

        $noBlackjack = new HandValue([
                new Card(Rank::from(Rank::THREE), Suit::from(Suit::CLUBS)),
                new Card(Rank::from(Rank::JACK), Suit::from(Suit::SPADES)),
                new Card(Rank::from(Rank::SEVEN), Suit::from(Suit::SPADES)),
        ]);

        $this->assertTrue($withBlackjack->isBlackjack());
        $this->assertFalse($noBlackjack->isBlackjack());
    }

    public function test_it_calculate_bust_correct(): void
    {
        $withBust = new HandValue([
            new Card(Rank::from(Rank::FIVE), Suit::from(Suit::DIAMONDS)),
            new Card(Rank::from(Rank::JACK), Suit::from(Suit::SPADES)),
            new Card(Rank::from(Rank::SEVEN), Suit::from(Suit::DIAMONDS)),
        ]);

        $noBust = new HandValue([
            new Card(Rank::from(Rank::THREE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::QUEEN), Suit::from(Suit::HEARTS)),
            new Card(Rank::from(Rank::THREE), Suit::from(Suit::SPADES)),
        ]);

        $this->assertTrue($withBust->isBust());
        $this->assertFalse($noBust->isBust());
    }

    public function test_it_value_ace_correct(): void
    {
        $withDefaultAce = new HandValue([
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
        ]);

        $withHardHandAce = new HandValue([
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::SPADES)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::SPADES)),
        ]);

        $this->assertSame($withDefaultAce->score(), 19);
        $this->assertSame($withHardHandAce->score(), 18);
    }

    public function test_same_values_are_equal(): void
    {
        $firstValue = new HandValue([
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
        ]);

        $secondValue = new HandValue([
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
        ]);

        $this->assertTrue($firstValue->equalsTo($secondValue));
    }

    public function test_different_values_are_not_equal(): void
    {
        $firstValue = new HandValue([
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
            new Card(Rank::from(Rank::TEN), Suit::from(Suit::DIAMONDS)),
        ]);

        $secondValue = new HandValue([
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
        ]);

        $this->assertFalse($firstValue->equalsTo($secondValue));
    }

}
