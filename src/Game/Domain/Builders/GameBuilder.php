<?php

namespace Src\Game\Domain\Builders;

use LogicException;
use Src\Game\Domain\Entities\Bet;
use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\Services\Dealer;
use Src\Game\Domain\ValueObjects\Card;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\BetId;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;
use Src\Game\Domain\ValueObjects\Money;

/**
 * Билдер для создания Game с настраиваемыми параметрами.
 */
final class GameBuilder
{

    public const DEFAULT_BET_AMOUNT = 100;
    public const DEFAULT_DECKS_COUNT_IN_SHOE = 3;
    private ?GameId $id = null;

    /** @var array<Player> */
    private array $players = [];

    /** @var array<Bet> */
    private array $playersBets = [];

    /** @var array<Hand> */
    private array $playersHands = [];
    private ?Hand $dealerHand = null;
    private ?Shoe $shoe = null;

    private bool $withBettingStage = false;
    private bool $withPlayersTurnStage = false;
    private bool $withDealerTurnStage = false;

    private bool $withGameFinishStage = false;

    public static function new(): self
    {
        return new self();
    }

    public function withId(GameId $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function withPlayer(Player $player): self
    {
        $player->joinTable();
        $this->players[] = $player;
        return $this;
    }

    /** @param array<Player> $players */
    public function withPlayers(array $players): self
    {
        foreach ($players as $player) {
            $player->joinTable();
            $this->players[] = $player;
        }

        return $this;
    }

    public function withDealerHand(Hand $dealerHand): self
    {
        $this->dealerHand = $dealerHand;
        return $this;
    }

    public function withShoe(Shoe $shoe): self
    {
        $this->shoe = $shoe;
        return $this;
    }

    /**
     * Добавляет стандартный шуз (если не указан вручную)
     */
    private function defaultShoe(): Shoe
    {
        return (new ShoeFactory(new DeckFactory))->create(self::DEFAULT_DECKS_COUNT_IN_SHOE);
    }

    /** @return array<Player> */
    private function defaultPlayers(): array
    {
        $players = [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
            new Player(PlayerId::generate(), "Alex"),
        ];

        foreach ($players as $player){
            $player->joinTable();
        }

        return $players;
    }

    private function defaultDealerHand(): Hand
    {
        return new Hand(HandId::generate());
    }

    public function withBettingStage(): self
    {
        $this->withBettingStage = true;
        return $this;
    }

    public function withPlayersTurnStage(): self
    {
        $this->withPlayersTurnStage = true;
        return $this;
    }

    public function withDealerTurnStage(): self
    {
        $this->withDealerTurnStage = true;
        return $this;
    }


    public function build(): Game
    {
        $id = $this->id ?? GameId::generate();
        $shoe = $this->shoe ?? $this->defaultShoe();
        $dealerHand = $this->dealerHand ?? $this->defaultDealerHand();
        $players = !empty($this->players) ? $this->players : $this->defaultPlayers();

        $game = new Game($id, $shoe, $dealerHand, $players);

        if ($this->withBettingStage){
            $this->setGameToBettingStage($game);
        }

        if ($this->withPlayersTurnStage){
            $this->setGameToBettingStage($game);
            $this->setGameToPlayersTurnStage($game);
        }

        return $game;
    }

    private function setGameToBettingStage(Game $game): void
    {
        $game->betStart();
    }

    private function setGameToPlayersTurnStage(Game $game): void
    {
        foreach ($game->players() as $player) {
            $bet = $this->playersBets[$player->id()->value()] ?? $this->playerDefaultBet();
            $player->placeBet($bet);

            if ($hand = $this->playersHands[$player->id()->value()]){
                foreach ($hand->cards() as $handCard){
                    $game->shoe()->drawConcrete($handCard);
                }
            } else {
                $hand = $this->playerDefaultHand($game);
            }

            $player->assignHand($hand);
        }


        if (empty($game->dealerHand()->cards())){
            $game->dealerHand()->receiveCard($game->shoe()->draw());
        }

        $game->playersTurnsStage();
    }

    public function withPlayerBet(PlayerId $playerId, Bet $bet): self
    {
        $this->playersBets[$playerId->value()] = $bet;
        return $this;
    }

    public function playerDefaultBet(): Bet
    {
        return new Bet(BetId::generate(), new Money(self::DEFAULT_BET_AMOUNT));
    }

    public function withPlayerHand(PlayerId $playerId, Hand $hand): self
    {
        $this->playersHands[$playerId->value()] = $hand;
        return $this;
    }

    public function playerDefaultHand(Game $game): Hand
    {
       return new Hand(HandId::generate(), [
           $game->shoe()->draw(),
           $game->shoe()->draw()
       ]);
    }
}


