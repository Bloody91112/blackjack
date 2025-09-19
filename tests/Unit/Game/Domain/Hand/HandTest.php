<?php

namespace Game\Domain\Hand;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class HandTest extends TestCase
{

    public function test_it_generates_unique_id(): void
    {
        $ids = [];
        for ($i = 0; $i < 100; $i++) {
            $ids[] = (new Hand())->id();
        }

        $this->assertSame($ids, array_unique($ids));
    }

    public function test_it_correctly_adds_cards(): void
    {
        $cards = [
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
            new Card(Rank::from(Rank::TEN), Suit::from(Suit::DIAMONDS)),
        ];

        $hand = new Hand();
        $hand->getCard($cards[0]);
        $this->assertSame($hand->value(), 11);

        $hand->getCard($cards[1]);
        $this->assertSame($hand->value(), 19);

        $hand->getCard($cards[2]);
        $this->assertSame($hand->value(), 19);
    }
}
