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
        Rank::from("1");
    }

    public function test_two_same_ranks_are_equal(): void
    {
        $first = Rank::from(Rank::JACK);
        $second = Rank::from(Rank::JACK);
        $this->assertTrue($first->equalsTo($second));
    }

    public function test_two_different_ranks_are_not_equal(): void
    {
        $first = Rank::from(Rank::QUEEN);
        $second = Rank::from(Rank::KING);
        $this->assertFalse($first->equalsTo($second));
    }

    public function test_it_returns_all_and_unique_values(): void
    {
        $this->assertSame(count(Rank::all()), count(array_unique(Rank::all())));
        $this->assertSame(count(Rank::all()), 13);
    }

}
