<?php

namespace Game\Domain\Card;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\ValueObjects\Rank;


class RankTest extends TestCase
{

    public function test_it_throws_exception_on_incorrect_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Rank("none");
    }

    public function test_two_same_ranks_are_equal(): void
    {
        $this->assertTrue(Rank::jack()->equalsTo(Rank::jack()));
    }

    public function test_two_different_ranks_are_not_equal(): void
    {
        $this->assertFalse(Rank::queen()->equalsTo(Rank::king()));
    }

    public function test_it_returns_all_and_unique_values(): void
    {
        $this->assertSame(count(Rank::all()), count(array_unique(Rank::all())));
        $this->assertSame(count(Rank::all()), 13);
    }

    public function test_it_indicates_ace_correctly(): void
    {
        $this->assertTrue(Rank::ace()->isAce());
    }

    public function test_it_returns_correct_base_value(): void
    {
        $this->assertSame(Rank::nine()->baseValue(), 9);
    }

}
