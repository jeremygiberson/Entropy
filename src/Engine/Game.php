<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 6:06 PM
 */

namespace JeremyGiberson\Entropy\Engine;


use JeremyGiberson\Entropy\Engine\Move\AttackMove;
use JeremyGiberson\Entropy\Engine\Strategy\StrategyInterface;
use JeremyGiberson\Entropy\Engine\Territory\Node;
use JeremyGiberson\Entropy\Engine\Territory\Territory;
use JeremyGiberson\Entropy\Engine\Territory\TerritoryInterface;
use JeremyGiberson\Entropy\Engine\Move\EndTurnMove;
use JeremyGiberson\Entropy\Factory\BoardFactory;
use JeremyGiberson\Entropy\Iterator\MutableIterator;
use RuntimeException;
use SplObjectStorage;

class Game
{
    /** @var Player[] */
    protected $players = [];
    /** @var  Scoreboard */
    protected $scoreboard;
    /** @var  Node[] */
    protected $territories;

    function __construct()
    {
        $this->territories = new \SplObjectStorage();
        $this->players = new MutableIterator();
    }


    /**
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        if(count($this->players) == 8)
        {
            throw new \RuntimeException("Can't add more players, already reached player limit");
        }

        $this->players[$this->players->last_key()+1] = $player;
    }

    /**
     * @param int $max_rounds
     */
    public function play($max_rounds = 100)
    {
        if(count($this->players) < 2)
        {
            throw new RuntimeException("game needs 2 to 8 players to run");
        }
        
        $this->scoreboard = new Scoreboard();
        $factory = new BoardFactory();
        $this->territories = $factory->create($this->players);

        $round = 0;
        do {
            $round++;

            foreach($this->players as $player)
            {
                $this->playerTurn($round, $player);

                // add dice
                $this->growPlayerTerritories($player);
            }

            // find conquered players
            $this->removeConqueredPlayers($round);

            // everyone has been conquered
            if(count($this->players) < 2)
            {
                break;
            }

        } while ($round < $max_rounds);

        // get ending round scores
        foreach($this->players as $player)
        {
            $this->removePlayer($round, $player);
        }
    }

    /**
     * @param Player $player
     * @return int
     */
    protected function countTerritories(Player $player)
    {
        $count = 0;
        /** @var TerritoryInterface $territory */
        foreach ($this->territories as $territory) {
            if(! $territory instanceof Territory) {
                continue;
            }
            /** @var $territory Territory */
            $count += $territory->getOwner() == $player ? 1 : 0;
        }
        return $count;
    }

    /**
     * @param Player $player
     * @param AttackMove $move
     * @return bool
     */
    protected function isValidAttack(Player $player, AttackMove $move)
    {
        $attacker = $move->getAttacker();
        $defender = $move->getDefender();

        if($player != $attacker->getOwner() || $player == $defender->getOwner())
        {
            return false;
        }

        if(! $attacker instanceof Territory || $attacker->getNumberOfDice() < 2)
        {
            return false;
        }

        return true;
    }

    /**
     * @param Player $player
     * @param AttackMove $move
     */
    protected function performAttack(Player $player, AttackMove $move)
    {
        /** @var Territory $attacker */
        $attacker = $move->getAttacker();
        /** @var Territory $defender */
        $defender = $move->getDefender();

        $attack = rand(1, 6 * $attacker->getNumberOfDice());
        $defense = rand(1, 6 * $defender->getNumberOfDice());

        if($attack > $defense) {
            $defender->setOwner($player);
            $defender->setNumberOfDice($attacker->getNumberOfDice() - 1);
            $attacker->setNumberOfDice(1);
        } else {
            $attacker->setNumberOfDice(1);
        }
    }

    /**
     * @return Scoreboard
     */
    public function getScoreboard()
    {
        return $this->scoreboard;
    }

    /**
     * @param Player $player
     * @return Territory[]
     */
    protected function getPlayerGrowthTerritories(Player $player)
    {
        $territories = [];
        foreach($this->territories as $territory)
        {
            if(!$territory instanceof Territory)
            {
                continue;
            }
            if($territory->getOwner() == $player
                && $territory->getNumberOfDice() < 8)
            {
                $territories[] = $territory;
            }
        }
        return $territories;
    }

    protected function growPlayerTerritories(Player $player)
    {
        $count = $this->countTerritories($player);

        do {
            $player_territories = $this->getPlayerGrowthTerritories($player);
            if(count($player_territories) < 1) {
                break;
            }

            $i = rand(0, count($player_territories)-1);
            $player_territories[$i]->setNumberOfDice(
                $player_territories[$i]->getNumberOfDice() + 1);
            $count--;
        } while ($count > 0);
    }

    protected function removeConqueredPlayers($round)
    {
        foreach($this->players as $check_player)
        {
            if($this->countTerritories($check_player) < 1)
            {
                $this->removePlayer($round, $check_player);
            }
        }
    }

    protected function removePlayer($round, Player $player)
    {
        // disqualify player
        $this->scoreboard->addScore($player,
            $round,
            $this->countTerritories($player));
        $this->players->remove($player);
    }

    protected function playerTurn($round, Player $player)
    {
        do {
            /** @var StrategyInterface $strategy */
            $strategy = $player->getStrategy();

            $move = $strategy->getMove($round, $player, $this->territories);

            if($move instanceof AttackMove) {
                if (!$this->isValidAttack($player, $move)) {
                    // end turn for invalid attack
                    $move = new EndTurnMove();
                } else {
                    $this->performAttack($player, $move);
                }
            } else if($move instanceof EndTurnMove) {
                // noop
            } else {
                // end turn for invalid move
                $move = new EndTurnMove();
            }
        } while (! $move instanceof EndTurnMove);
    }
}