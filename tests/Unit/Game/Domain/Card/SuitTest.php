<?php

namespace Game\Domain\Card;


use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\ValueObjects\Suit;


class SuitTest extends TestCase
{

    public function test_it_throws_exception_on_incorrect_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Suit("Wrong");
    }

    public function test_two_same_suits_are_equal(): void
    {
        $this->assertTrue(Suit::clubs()->equalsTo(Suit::clubs()));
    }

    public function test_two_different_suits_are_not_equal(): void
    {
        $this->assertFalse(Suit::hearts()->equalsTo(Suit::clubs()));
    }

    public function test_it_returns_all_and_unique_values(): void
    {
        $this->assertSame(count(Suit::all()), count(array_unique(Suit::all())));
        $this->assertSame(count(Suit::all()), 4);
    }

}
