<?php

namespace Tests\Unit\Game\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\ValueObjects\Money;

class MoneyTest extends TestCase
{
    public function test_it_doesnt_creates_with_negative_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Money(-100);
    }

    public function test_it_adds_amount_correctly(): void
    {
        $money = new Money(100);
        $money = $money->add(new Money(30));
        $this->assertSame($money->amount(), 130);
    }

    public function test_same_money_are_equals(): void
    {
        $firstMoney = new Money(100);
        $secondMoney = new Money(100);
        $this->assertTrue($firstMoney->equalsTo($secondMoney));
    }
}
