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
            new Card(Rank::jack(), Suit::hearts()),
            Suit::clubs()
        ];
        new HandValue($cards);
    }

    public function test_it_count_correct_score_with_one_card(): void
    {
        $handValue = new HandValue([
            new Card(Rank::jack(), Suit::hearts()),
        ]);
        $this->assertSame($handValue->score(), 10);
    }

    public function test_it_count_correct_score_with_two_cards(): void
    {
        $handValue = new HandValue([
            new Card(Rank::jack(), Suit::hearts()),
            new Card(Rank::queen(), Suit::hearts()),
        ]);

        $this->assertSame($handValue->score(), 20);
    }

    public function test_it_recount_correct(): void
    {
        $handValue = new HandValue([
            new Card(Rank::four(), Suit::clubs()),
            new Card(Rank::three(), Suit::spades()),
        ]);

        $additionalCard = new Card(Rank::seven(), Suit::diamonds());

        $handValueAfterRecount = $handValue->add($additionalCard);

        $this->assertSame($handValueAfterRecount->score(), 14);
    }

    public function test_it_calculate_blackjack_correct(): void
    {
        $withBlackjack = new HandValue([
                new Card(Rank::four(), Suit::clubs()),
                new Card(Rank::jack(), Suit::spades()),
                new Card(Rank::seven(), Suit::spades()),
        ]);

        $noBlackjack = new HandValue([
                new Card(Rank::three(), Suit::clubs()),
                new Card(Rank::jack(), Suit::spades()),
                new Card(Rank::seven(), Suit::spades()),
        ]);

        $this->assertTrue($withBlackjack->isBlackjack());
        $this->assertFalse($noBlackjack->isBlackjack());
    }

    public function test_it_calculate_bust_correct(): void
    {
        $withBust = new HandValue([
            new Card(Rank::five(), Suit::diamonds()),
            new Card(Rank::jack(), Suit::spades()),
            new Card(Rank::seven(), Suit::diamonds()),
        ]);

        $noBust = new HandValue([
            new Card(Rank::three(), Suit::clubs()),
            new Card(Rank::queen(), Suit::hearts()),
            new Card(Rank::three(), Suit::spades()),
        ]);

        $this->assertTrue($withBust->isBust());
        $this->assertFalse($noBust->isBust());
    }

    public function test_it_value_ace_correct(): void
    {
        $withDefaultAce = new HandValue([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::eight(), Suit::hearts()),
        ]);

        $withHardHandAce = new HandValue([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::eight(), Suit::spades()),
            new Card(Rank::eight(), Suit::spades()),
        ]);

        $this->assertSame($withDefaultAce->score(), 19);
        $this->assertSame($withHardHandAce->score(), 18);
    }

    public function test_same_values_are_equal(): void
    {
        $firstValue = new HandValue([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::eight(), Suit::hearts()),
        ]);

        $secondValue = new HandValue([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::eight(), Suit::hearts()),
        ]);

        $this->assertTrue($firstValue->equalsTo($secondValue));
    }

    public function test_different_values_are_not_equal(): void
    {
        $firstValue = new HandValue([
            new Card(Rank::ace(), Suit::clubs()),
            new Card(Rank::eight(), Suit::hearts()),
            new Card(Rank::ten(), Suit::diamonds()),
        ]);

        $secondValue = new HandValue([
            new Card(Rank::eight(), Suit::hearts()),
        ]);

        $this->assertFalse($firstValue->equalsTo($secondValue));
    }

}
