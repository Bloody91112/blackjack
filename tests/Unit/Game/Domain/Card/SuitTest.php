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
        Suit::from("Wrong");
    }

    public function test_two_same_suits_are_equal(): void
    {
        $first = Suit::from(Suit::CLUBS);
        $second = Suit::from(Suit::CLUBS);
        $this->assertTrue($first->equalsTo($second));
    }

    public function test_two_different_suits_are_not_equal(): void
    {
        $first = Suit::from(Suit::HEARTS);
        $second = Suit::from(Suit::CLUBS);
        $this->assertFalse($first->equalsTo($second));
    }

}
