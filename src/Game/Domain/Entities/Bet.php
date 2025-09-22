<?php

namespace Src\Game\Domain\Entities;

use Src\Game\Domain\Enum\BetStatus;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Money;

class Bet
{
    public function __construct(
        private BetId $id,
        private Money $money,
        private BetStatus $status = BetStatus::Pending
    )
    {}

    public function win(): void
    {
        $this->status = BetStatus::Won;
    }

    public function lose(): void
    {
        $this->status = BetStatus::Lost;
    }

    public function money(): Money
    {
        return $this->money;
    }

    public function status(): BetStatus
    {
        return $this->status;
    }

    public function id(): BetId
    {
        return $this->id;
    }
}
