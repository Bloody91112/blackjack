<?php

namespace Src\Game\Domain\ValueObjects;

use InvalidArgumentException;

class HandValue
{
    private const BLACKJACK_VALUE = 21;
    private const HARD_HAND_ACE_DIFF = 10;

    private bool $isBust = false;
    private bool $isBlackjack = false;

    private int $score = 0;
    public function __construct(
        /** @var array<Card> $cards*/
        private array $cards = []
    ){
        foreach ($cards as $card){
            if (!($card instanceof Card)){
                throw new InvalidArgumentException("Wrong card type");
            }
        }
        $this->calculate();
    }

    private function calculate(): void
    {
        $aces = 0;
        $sum = 0;

        foreach ($this->cards as $card){
            $rankValue = $card->rank()->baseValue();
            $sum += $this->score + $rankValue;
            if ($card->rank()->isAce()){
                $aces++;
            }
        }

        while ($sum > self::BLACKJACK_VALUE && $aces > 0){
            $sum -= self::HARD_HAND_ACE_DIFF;
            $aces--;
        }

        $this->score = $sum;

        $this->isBust = $this->score > self::BLACKJACK_VALUE;
        $this->isBlackjack = $this->score === self::BLACKJACK_VALUE;
    }

    public function recount(Card $card): self
    {
        return new self ([...$this->cards, $card]);
    }

    public function score(): int
    {
        return $this->score;
    }

    public function isBlackjack(): bool
    {
        return $this->isBlackjack;
    }

    public function isBust(): bool
    {
        return $this->isBust;
    }

    public function equalsTo(self $other): bool
    {
        foreach ($this->cards as $card){
            $foundSame = false;
            foreach ($other->cards as $otherCard){
                if ($card->equalsTo($otherCard)){
                    $foundSame = true;
                    break;
                };
            }
            if (!$foundSame) return false;
        }
        return true;
    }

    public function hasAce(): bool
    {
        foreach ($this->cards as $card){
            if ($card->rank()->isAce()){
                return true;
            }
        }
        return false;
    }

    public function __toString(): string
    {
        return $this->score;
    }

}
