<?php

namespace Game\Domain\Player;

use LogicException;
use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Enum\BetStatus;
use Src\Game\Domain\Enum\PlayerResult;
use Src\Game\Domain\Enum\PlayerState;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

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

    public function test_it_changes_state_when_it_joins_table(): void
    {
        $this->joinTableStep();
        $this->assertSame($this->player->state(), PlayerState::SittingAtTheTable);
    }

    public function test_it_changes_state_when_it_start_betting(): void
    {
        $this->startBettingStep();
        $this->assertSame($this->player->state(), PlayerState::ChoosingABet);
    }

    public function test_it_changes_state_when_it_place_bet(): void
    {
        $this->placingABetStep();
        $this->assertSame($this->player->state(), PlayerState::PlacedABet);
    }

    public function test_it_changes_state_when_it_start_turn(): void
    {
        $this->startTurnStep();
        $this->assertSame($this->player->state(), PlayerState::Active);
        $this->assertTrue($this->player->isActive());
    }

    public function test_it_changes_state_when_it_stand(): void
    {
        $this->standStep();
        $this->assertSame($this->player->state(), PlayerState::Standing);
        $this->assertTrue($this->player->isStanding());
    }

    public function test_it_handle_hit_with_blackjack_correctly(): void
    {
        $this->startTurnStep();

        $this->player->hit(new Card(Rank::ten(), Suit::spades()));
        $this->player->hit(new Card(Rank::ace(), Suit::spades()));

        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Blackjack);
        $this->assertSame($this->player->bet()->status(), BetStatus::Won);
    }

    public function test_it_handle_hit_with_bust_correctly(): void
    {
        $this->startTurnStep();

        $this->player->hit(new Card(Rank::ten(), Suit::spades()));
        $this->player->hit(new Card(Rank::three(), Suit::spades()));
        $this->player->hit(new Card(Rank::ten(), Suit::spades()));

        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Bust);
        $this->assertSame($this->player->bet()->status(), BetStatus::Lost);
    }

    public function test_it_handle_hit_without_bust_and_blackjack_correctly(): void
    {
        $this->startTurnStep();

        $this->player->hit(new Card(Rank::ten(), Suit::spades()));
        $this->player->hit(new Card(Rank::three(), Suit::spades()));

        $this->assertSame($this->player->state(), PlayerState::Active);
        $this->assertSame($this->player->result(), null);
        $this->assertSame($this->player->bet()->status(), BetStatus::Pending);
    }

    public function test_it_handle_lost_result_correctly(): void
    {
        $this->standStep();
        $this->player->lose();

        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Lost);
        $this->assertSame($this->player->bet()->status(), BetStatus::Lost);
    }

    public function test_it_handle_win_result_correctly(): void
    {
        $this->standStep();
        $this->player->win();

        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Won);
        $this->assertSame($this->player->bet()->status(), BetStatus::Won);
    }

    public function test_it_handle_push_result_correctly(): void
    {
        $this->standStep();
        $this->player->push();

        $this->assertSame($this->player->state(), PlayerState::Finished);
        $this->assertSame($this->player->result(), PlayerResult::Push);
        $this->assertSame($this->player->bet()->status(), BetStatus::Push);
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
        $this->placingABetStep();
        $this->assertInstanceOf(Bet::class, $this->player->bet());
    }

    public function test_it_throws_exception_on_second_bet_placing(): void
    {
        $this->placingABetStep();
        $secondBet = new Bet(BetId::generate(), new Money(100));;

        $this->expectException(LogicException::class);
        $this->player->placeBet($secondBet);
    }


    public function test_it_cant_join_game_with_incorrect_status(): void
    {
        $this->startBettingStep();
        $this->expectException(LogicException::class);
        $this->player->startBetting();
    }



    private function joinTableStep(): void
    {
        $this->player->joinTable();
    }

    private function startBettingStep(): void
    {
        $this->joinTableStep();
        $this->player->startBetting();
    }

    private function placingABetStep(): void
    {
        $this->startBettingStep();
        $this->player->placeBet($this->bet);
    }

    private function startTurnStep(): void
    {
        $this->placingABetStep();
        $this->player->assignHand($this->hand);
        $this->player->startTurn();
    }

    private function standStep(): void
    {
        $this->startTurnStep();
        $this->player->hit(new Card(Rank::ten(), Suit::spades()));
        $this->player->hit(new Card(Rank::three(), Suit::spades()));
        $this->player->stand();
    }

}

