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
use SplObjectStorage;

class Game
{
    /** @var SplObjectStorage */
    protected $players = [];
    /** @var  Scoreboard */
    protected $scoreboard;
    /** @var  Node[] */
    protected $territories;

    function __construct()
    {
        $this->territories = new \SplObjectStorage();
        $this->players = new \SplObjectStorage();
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

        $this->players->attach($player);
    }

    public function play($max_rounds = 100)
    {
        $this->scoreboard = new Scoreboard();
        $factory = new BoardFactory();
        $this->territories = $factory->create($this->players);

        $active_players = $this->players;

        $round = 0;
        do {
            $round++;

            $remove_players = [];

            foreach($active_players as $player)
            {
                do {
                    /** @var StrategyInterface $strategy */
                    $strategy = $player->getStrategy();
                    //var_dump($player, $strategy);
                    $move = $strategy->getMove($round, $player, $this->territories);

                    if($move instanceof AttackMove) {
                        if (!$this->isValidAttack($player, $move)) {
                            // disqualify player
                            $this->scoreboard->addScore($player,
                                $round,
                                $this->countTerritories($player));
                            $remove_players[] = $player;
                            $move = new EndTurnMove();
                        } else {
                            $this->performAttack($player, $move);
                        }
                    } else if($move instanceof EndTurnMove) {
                        // noop
                    } else {
                        // disqualify player
                        $this->scoreboard->addScore($player,
                            $round,
                            $this->countTerritories($player));
                        $remove_players[] = $player;
                        $move = new EndTurnMove();
                    }
                } while (! $move instanceof EndTurnMove);
            }

            // find conquered players
            foreach($active_players as $check_player)
            {
                if($this->countTerritories($check_player) < 1)
                {
                    // disqualify player
                    $this->scoreboard->addScore($check_player,
                        $round,
                        $this->countTerritories($check_player));
                    $remove_players[] = $check_player;
                }
            }

            // remove conquered/disqualified players
            foreach($remove_players as $player)
            {
                $active_players->detach($player);
            }

            // everyone has been conquered
            if(count($active_players) < 2)
            {
                break;
            }

            // add dice
            // find conquered players
            foreach($active_players as $check_player)
            {
                $count = $this->countTerritories($check_player);

                do {
                    $player_territories = $this->getPlayerGrowthTerritories($check_player);
                    if(count($player_territories) < 1) {
                        break;
                    }

                    $i = rand(0, count($player_territories)-1);
                    $player_territories[$i]->setNumberOfDice(
                        $player_territories[$i]->getNumberOfDice() + 1);
                    $count--;
                } while ($count > 0);
            }

        } while ($round < $max_rounds);

        // get ending round scores
        foreach($active_players as $player)
        {
            echo "adding score\n";
            $this->scoreboard->addScore($player,
                $round,
                $this->countTerritories($player));
        }
    }

    /**
     * @param Player $player
     * @return int
     */
    protected function countTerritories($player)
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
    protected function isValidAttack($player, AttackMove $move)
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
    protected function performAttack($player, AttackMove $move)
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
    protected function getPlayerGrowthTerritories($player)
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
}