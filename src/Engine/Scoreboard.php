<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 6:14 PM
 */

namespace JeremyGiberson\Entropy\Engine;


use SplObjectStorage;

/**
 * Class Scoreboard
 * @package JeremyGiberson\Entropy\Engine
 */
class Scoreboard
{
    /** @var  SplObjectStorage */
    protected $player_scores;

    function __construct()
    {
        $this->player_scores = new SplObjectStorage();
    }

    /**
     * @param Player $player
     * @param $last_round
     * @param $territories_held
     */
    public function addScore(Player $player, $last_round, $territories_held)
    {
        $this->player_scores->attach($player, [
            'last_round' => $last_round,
            'territories_held' => $territories_held
        ]);
    }

    /**
     * @return string
     */
    public function render()
    {
        $output = "Player\tLast Round\tTerritories Held\n";
        /**
         * @var Player $player
         * @var array $data
         */
        foreach($this->player_scores as $player)
        {
            $data = $this->player_scores[$player];
            $output .= sprintf("%s\t%s\t\t%s\n",
                $player->getName(),
                $data['last_round'],
                $data['territories_held']);
        }
        return $output;
    }
}