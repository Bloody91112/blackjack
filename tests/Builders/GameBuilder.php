<?php

namespace Tests\Builders;

use Src\Game\Domain\Entities\Game;
use Src\Game\Domain\Entities\Hand;
use Src\Game\Domain\Entities\Player;
use Src\Game\Domain\Entities\Shoe;
use Src\Game\Domain\Factories\DeckFactory;
use Src\Game\Domain\Factories\ShoeFactory;
use Src\Game\Domain\ValueObjects\HandValue;
use Src\Game\Domain\ValueObjects\Ids\GameId;
use Src\Game\Domain\ValueObjects\Ids\HandId;
use Src\Game\Domain\ValueObjects\Ids\PlayerId;

/**
 * Тестовый билдер для создания Game с настраиваемыми параметрами.
 */
final class GameBuilder
{
    private ?GameId $id = null;
    private array $players = [];
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
        $this->players[] = $player;
        return $this;
    }

    public function withPlayers(array $players): self
    {
        $this->players = $players;
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
        return (new ShoeFactory(new DeckFactory))->create(3);
    }

    /** @return array<Player> */
    private function defaultPlayers(): array
    {
        return [
            new Player(PlayerId::generate(), "John"),
            new Player(PlayerId::generate(), "Bob"),
            new Player(PlayerId::generate(), "Alex"),
        ];
    }

    private function defaultDealerHand(): Hand
    {
        return new Hand(HandId::generate(), new HandValue());
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

        return new Game($id, $shoe, $dealerHand, $players);
    }
}

