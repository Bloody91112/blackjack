<?php

namespace Game\Domain\Hand;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class HandTest extends TestCase
{
    private array $cards;
    private Hand $hand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cards = [
            new Card(Rank::from(Rank::ACE), Suit::from(Suit::CLUBS)),
            new Card(Rank::from(Rank::EIGHT), Suit::from(Suit::HEARTS)),
            new Card(Rank::from(Rank::TEN), Suit::from(Suit::DIAMONDS)),
        ];
        $handId = HandId::generate();
        $handValue = new HandValue();
        $this->hand =  new Hand($handId, $handValue);
    }

    public function test_it_correctly_adds_cards(): void
    {
        $this->hand->receiveCard($this->cards[0]);
        $this->assertSame($this->hand->value()->score(), 11);

        $this->hand->receiveCard($this->cards[1]);
        $this->assertSame($this->hand->value()->score(), 19);

        $this->hand->receiveCard($this->cards[2]);
        $this->assertSame($this->hand->value()->score(), 19);
    }

    public function test_it_correctly_returns_cards(): void
    {
        $this->hand->receiveCard($this->cards[0]);
        $this->hand->receiveCard($this->cards[1]);
        $this->hand->receiveCard($this->cards[2]);

        $handCards = $this->hand->cards();
        $this->assertTrue($handCards[0]->equalsTo($this->cards[0]));
        $this->assertTrue($handCards[1]->equalsTo($this->cards[1]));
        $this->assertTrue($handCards[2]->equalsTo($this->cards[2]));
    }
}
