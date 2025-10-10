<?php

namespace Tests\Unit\Game\Domain;

use PHPUnit\Framework\TestCase;
use Src\Game\Domain\Builders\GameBuilder;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Enum\GameState;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\GameFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\Deck;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Ids\ShoeId;
use Src\Game\Domain\ValueObjects\Money;
use Src\Game\Domain\ValueObjects\Rank;
use Src\Game\Domain\ValueObjects\Suit;

class GameBuilderTest extends TestCase
{

    public function test_builder_creates_game_with_default_params(): void
    {
        $game = GameBuilder::new()->build();

        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame($game->state(), GameState::Created);
        $this->assertCount(3, $game->players());
        $this->assertCount(3, $game->shoe()->decks());
    }

    public function test_builder_creates_game_with_specific_params(): void
    {
        $id = GameId::generate();
        $dealerHand = new Hand(HandId::generate());
        $shoe = (new ShoeFactory(new DeckFactory))->create(5);

        $players = [
            new Player(PlayerId::generate(), "Anna"),
            new Player(PlayerId::generate(), "Alice"),
        ];

        $game = GameBuilder::new()
            ->withId($id)
            ->withPlayers($players)
            ->withDealerHand($dealerHand)
            ->withShoe($shoe)
            ->build();

        $this->assertCount(2, $game->players());
        $this->assertTrue($game->id()->equals($id));
        $this->assertTrue($game->dealerHand()->id()->equals($dealerHand->id()));
        $this->assertCount(5, $game->shoe()->decks());

    }

    public function test_it_creates_correct_game_at_betting_stage(): void
    {
        $game = GameBuilder::new()
            ->withBettingStage()
            ->build();

        $this->assertSame($game->state(), GameState::Betting);
    }

    public function test_it_creates_correct_game_at_players_turn_stage_with_default_params(): void
    {
        $game = GameBuilder::new()
            ->withPlayersTurnStage()
            ->build();

        foreach ($game->players() as $player){
            $this->assertCount(2, $player->hand()->cards());
            $this->assertSame($player->bet()->money()->amount(), GameBuilder::DEFAULT_BET_AMOUNT);
        }

        $this->assertCount(1, $game->dealerHand()->cards());
        $this->assertSame($game->state(), GameState::PlayersTurn);

    }

    public function test_it_creates_correct_game_at_players_turn_stage_with_specific_params(): void
    {
        $anna = new Player(PlayerId::generate(), "Anna");
        $annaBet = new Bet(BetId::generate(), new Money(500));
        $annaCards = [
            Card::from(Rank::ten(), Suit::clubs()),
            Card::from(Rank::ten(), Suit::diamonds()),
        ];
        $annaHand = new Hand(HandId::generate(), $annaCards);

        $alice = new Player(PlayerId::generate(), "Alice");

        $dealerHand = new Hand(HandId::generate(), [
            Card::from(Rank::ten(), Suit::clubs()),
        ]);

        $game = GameBuilder::new()
            ->withPlayersTurnStage()
            ->withPlayers([$anna, $alice])
            ->withPlayerBet($anna->id(), $annaBet)
            ->withPlayerHand($anna->id(), $annaHand)
            ->withDealerHand($dealerHand)
            ->build();

        $foundAnna = $game->findPlayer($anna->id());

        $this->assertTrue($foundAnna->bet()->id()->equals($annaBet->id()));
        $this->assertTrue($foundAnna->bet()->money()->equalsTo($annaBet->money()));

        $this->assertTrue($foundAnna->hand()->id()->equals($annaHand->id()));
        $this->assertTrue($foundAnna->hand()->cards()[0]->equalsTo($annaCards[0]));
        $this->assertTrue($foundAnna->hand()->cards()[1]->equalsTo($annaCards[1]));

        $this->assertTrue($game->dealerHand()->id()->equals($dealerHand->id()));
        $this->assertTrue($game->dealerHand()->cards()[0]->equalsTo($dealerHand->cards()[0]));

    }


}
