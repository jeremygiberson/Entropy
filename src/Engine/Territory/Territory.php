<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 10:09 PM
 */

namespace JeremyGiberson\Entropy\Engine\Territory;


use JeremyGiberson\Entropy\Engine\Player;

class Territory extends Node implements TerritoryInterface
{
    /** @var  int */
    protected $number_of_dice = 0;
    /** @var  Player */
    protected $owner;

    /**
     * @return int
     */
    public function getNumberOfDice()
    {
        return $this->number_of_dice;
    }

    /**
     * @param int $number_of_dice
     */
    public function setNumberOfDice($number_of_dice)
    {
        $this->number_of_dice = $number_of_dice;
    }

    /**
     * @return Player
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Player $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }


}