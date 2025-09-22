<?php

namespace Game\Domain\Player;

use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Enum\PlayerResult;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

class PlayerTest extends TestCase
{

    private Player $player;
    private Hand $hand;
    private Bet $bet;
    protected function setUp(): void
    {
        parent::setUp();
        $this->player = new Player(PlayerId::generate(), "John");
        $this->hand = new Hand(HandId::generate(), new HandValue());
        $this->bet = new Bet(BetId::generate(), new Money(100));
    }

    public function test_it_creates_with_correct_id(): void
    {
        $id = PlayerId::generate();
        $player = new Player($id, "John");
        $this->assertTrue($player->id()->equals($id));
    }

    public function test_it_returns_nickname(): void
    {
        $this->assertSame($this->player->nickname(), "John");
    }

    public function test_it_assigns_hand(): void
    {
        $this->player->assignHand($this->hand);
        $this->assertInstanceOf(Hand::class, $this->player->hand());
    }

    public function test_it_throws_exception_on_second_hand_assign(): void
    {
        $this->player->assignHand($this->hand);
        $secondHand = new Hand(HandId::generate(), new HandValue());

        $this->expectException(LogicException::class);
        $this->player->assignHand($secondHand);
    }

    public function test_it_throws_exception_on_hand_missing(): void
    {
        $this->expectException(LogicException::class);
        $this->player->hand();
    }

    public function test_it_places_bet(): void
    {
        $this->player->placeBet($this->bet);
        $this->assertInstanceOf(Bet::class, $this->player->bet());
    }

    public function test_it_throws_exception_on_second_bet_placing(): void
    {
        $this->player->placeBet($this->bet);
        $secondBet = new Bet(BetId::generate(), new Money(100));;

        $this->expectException(LogicException::class);
        $this->player->placeBet($secondBet);
    }

    public function test_it_throws_exception_on_bet_missing(): void
    {
        $this->expectException(LogicException::class);
        $this->player->bet();
    }

    public function test_it_accept_correct_state_when_joining_game(): void
    {
        $this->player->join();
        $this->assertSame($this->player->state(), PlayerState::Active);
    }

    public function test_cant_join_game_with_incorrect_status(): void
    {
        $this->player->join();

        $this->expectException(LogicException::class);
        $this->player->join();
    }

    public function test_it_accepts_correct_status_and_result_on_bust(): void
    {
        $this->player->bust();

        $this->assertSame($this->player->state(), PlayerState::Busted);
        $this->assertSame($this->player->result(), PlayerResult::Lost);
    }

    public function test_it_accepts_correct_state_on_stand(): void
    {
        $this->player->stand();
        $this->assertSame($this->player->state(), PlayerState::Standing);
    }

    public function test_it_accepts_correct_state_and_result_on_finish(): void
    {
        $this->player->finished(PlayerResult::Blackjack);
        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Blackjack);
    }
}

