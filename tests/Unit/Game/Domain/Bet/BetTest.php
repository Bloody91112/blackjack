<?php

namespace Tests\Unit\Game\Domain\Bet;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Enum\BetStatus;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Money;

class BetTest extends TestCase
{
    private Bet $bet;
    protected function setUp(): void
    {
        parent::setUp();
        $this->bet = new Bet(BetId::generate(), new Money(100));
    }

    public function test_creates_correctly(): void
    {
        $this->assertInstanceOf(Bet::class, $this->bet);
    }

    public function test_it_creates_with_correct_status(): void
    {
        $this->assertSame($this->bet->status(), BetStatus::Pending);
    }

    public function test_it_accept_correct_status_on_lose(): void
    {
        $this->bet->lose();
        $this->assertSame($this->bet->status(), BetStatus::Lost);
    }

    public function test_it_accept_correct_status_on_win(): void
    {
        $this->bet->win();
        $this->assertSame($this->bet->status(), BetStatus::Won);
    }
}
