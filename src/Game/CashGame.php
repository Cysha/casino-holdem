<?php

namespace Cysha\Casino\Holdem\Game;

use Cysha\Casino\Cards\Deck;
use Cysha\Casino\Exceptions\GameException;
use Cysha\Casino\Game\Chips;
use Cysha\Casino\Game\Client;
use Cysha\Casino\Game\Contracts\Game;
use Cysha\Casino\Game\Contracts\GameParameters;
use Cysha\Casino\Game\PlayerCollection;
use Cysha\Casino\Game\TableCollection;
use Cysha\Casino\Holdem\Cards\Evaluators\SevenCard;
use Ramsey\Uuid\UuidInterface;

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
     * @var DefaultParameters
     */
    private $rules;

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
     * @param UuidInterface  $id
     * @param string         $name
     * @param GameParameters $rules
     */
    public function __construct(UuidInterface $id, string $name, GameParameters $rules)
    {
        $this->id = $id;
        $this->name = $name;
        $this->players = PlayerCollection::make();
        $this->tables = TableCollection::make();
        $this->rules = $rules;
    }

    /**
     * @param UuidInterface  $id
     * @param string         $name
     * @param GameParameters $rules
     *
     * @return CashGame
     */
    public static function setUp(UuidInterface $id, string $name, GameParameters $rules)
    {
        return new self($id, $name, $rules);
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
     * @return GameParameters
     */
    public function rules(): GameParameters
    {
        return $this->rules;
    }

    /**
     * @return PlayerCollection
     */
    public function players(): PlayerCollection
    {
        return $this->players;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return TableCollection
     */
    public function tables(): TableCollection
    {
        return $this->tables;
    }

    /**
     * @param Client $client
     * @param Chips  $buyinAmount
     *
     * @throws GameException
     */
    public function registerPlayer(Client $client, Chips $buyinAmount = null)
    {
        $buyinAmount = $buyinAmount ?? $this->rules()->minimumBuyIn();

        $playerRegistered = $this->players()
            ->filter(function (Client $player) use ($client) {
                return $client->name() === $player->name();
            });

        if ($playerRegistered->count() !== 0) {
            throw GameException::alreadyRegistered($client, $this);
        }

        if ($buyinAmount->amount() > $client->wallet()->amount()) {
            throw GameException::insufficientFunds($client, $this);
        }

        $client->wallet()->subtract($buyinAmount);

        $addPlayer = Player::fromClient($client, $buyinAmount);
        $this->players()->push($addPlayer);
    }

    public function removePlayer(Client $client)
    {
        $player = $this->players()
            ->filter(function (Player $player) use ($client) {
                return $player->name() === $client->name();
            })
            ->first()
        ;

        if ($player === null) {
            throw GameException::notRegistered($client, $this);
        }

        $client->wallet()->add($player->chipstack());

        $this->players = $this->players()
            ->reject(function (Player $player) use ($client) {
                return $player->name() === $client->name();
            })
            ->values();
    }

    public function assignPlayersToTables()
    {
        $groupedPlayers = $this->players()
        //->shuffle()
            ->chunk($this->rules()->tableSize())
            ->map(function (PlayerCollection $players) {
                $dealer = Dealer::startWork(new Deck(), new SevenCard());

                return Table::setUp($dealer, $players);
            })
            ->toArray();

        $this->tables = TableCollection::make($groupedPlayers);
    }
}
