<?php

namespace xLink\Poker\Game;

use Ramsey\Uuid\UuidInterface;
use xLink\Poker\Cards\Deck;
use xLink\Poker\Cards\Evaluators\SevenCard;
use xLink\Poker\Client;
use xLink\Poker\Exceptions\GameException;
use xLink\Poker\Table;

final class CashGame implements Game
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Chips
     */
    private $minimumBuyIn;

    /**
     * @var PlayerCollection
     */
    private $players;

    /**
     * @var TableCollection
     */
    protected $tables;

    /**
     * CashGame constructor.
     *
     * @param UuidInterface $id
     * @param string        $name
     * @param Chips         $minimumBuyIn
     */
    public function __construct(UuidInterface $id, string $name, Chips $minimumBuyIn)
    {
        $this->id = $id;
        $this->name = $name;
        $this->players = PlayerCollection::make();
        $this->minimumBuyIn = $minimumBuyIn;
        $this->tables = TableCollection::make();
    }

    /**
     * @param UuidInterface $id
     * @param string        $name
     * @param Chips         $minimumBuyIn
     *
     * @return CashGame
     */
    public static function setUp(UuidInterface $id, string $name, Chips $minimumBuyIn)
    {
        return new self($id, $name, $minimumBuyIn);
    }

    /**
     * @return UuidInterface
     */
    public function id(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Chips
     */
    public function minimumBuyIn(): Chips
    {
        return $this->minimumBuyIn;
    }

    public function players(): PlayerCollection
    {
        return $this->players;
    }

    /**
     * @param Client $client
     * @param Chips  $buyinAmount
     *
     * @throws GameException
     */
    public function registerPlayer(Client $client, Chips $buyinAmount = null)
    {
        $buyinAmount = $buyinAmount ?? $this->minimumBuyIn();

        $playersWithTheSameName = $this->players()
            ->filter(function (Client $player) use ($client) {
                return $client->name() === $player->name();
            });

        if ($playersWithTheSameName->count() !== 0) {
            throw GameException::alreadyRegistered($client, $this);
        }

        if ($buyinAmount->amount() > $client->wallet()->amount()) {
            throw GameException::insufficientFunds($client, $this);
        }

        $client->wallet()->subtract($buyinAmount);

        $addPlayer = Player::fromClient($client, $buyinAmount);
        $this->players()->push($addPlayer);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * TODO: Refactor out table creation to make it more readable.
     */
    public function assignPlayersToTables()
    {
        $groupedPlayers = $this->players()->shuffle()->chunk(9);

        $this->tables = TableCollection::make($groupedPlayers->map(function (PlayerCollection $players) {
            $dealer = Dealer::startWork(new Deck(), new SevenCard());

            return Table::setUp($dealer, $players);
        })->toArray());
    }

    /**
     * @return TableCollection
     */
    public function tables(): TableCollection
    {
        return $this->tables;
    }
}
