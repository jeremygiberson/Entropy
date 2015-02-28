<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 7:08 PM
 */

namespace JeremyGiberson\Entropy\Engine\Move;


use JeremyGiberson\Entropy\Engine\Territory\TerritoryInterface;

class AttackMove implements MoveInterface
{
    /** @var  TerritoryInterface */
    protected $attacker;
    /** @var  TerritoryInterface */
    protected $defender;

    function __construct(TerritoryInterface $attacker, TerritoryInterface $defender)
    {
        $this->attacker = $attacker;
        $this->defender = $defender;
    }

    /**
     * @return TerritoryInterface
     */
    public function getAttacker()
    {
        return $this->attacker;
    }

    /**
     * @return TerritoryInterface
     */
    public function getDefender()
    {
        return $this->defender;
    }

}