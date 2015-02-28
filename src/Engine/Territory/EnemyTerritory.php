<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 10:33 PM
 */

namespace JeremyGiberson\Entropy\Engine\Territory;


use JeremyGiberson\Entropy\Engine\Player;

class EnemyTerritory extends Node implements TerritoryInterface
{
    /** @var  Player */
    protected $owner;

    function __construct($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }
}