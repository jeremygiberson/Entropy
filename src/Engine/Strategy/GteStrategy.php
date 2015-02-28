<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 6:05 PM
 */

namespace JeremyGiberson\Entropy\Engine\Strategy;


use JeremyGiberson\Entropy\Engine\Move\AttackMove;
use JeremyGiberson\Entropy\Engine\Move\EndTurnMove;
use JeremyGiberson\Entropy\Engine\Move\MoveInterface;
use JeremyGiberson\Entropy\Engine\Player;
use JeremyGiberson\Entropy\Engine\Territory\Node;
use JeremyGiberson\Entropy\Engine\Territory\Territory;

class GteStrategy implements StrategyInterface {


    /**
     * @param int $round
     * @param Player $player
     * @param Node[] $territories
     * @return MoveInterface
     */
    public function getMove($round, $player, $territories)
    {

        foreach($territories as $territory)
        {
            if(!$territory instanceof Territory)
            {
                continue;
            }

            if($territory->getOwner() != $player)
            {
                continue;
            }

            if($territory->getNumberOfDice() < 2)
            {
                continue;
            }

            foreach($territory->getNeighbors() as $neighbor)
            {
                if(!$neighbor instanceof Territory
                    || $neighbor->getOwner() == $player
                    || $neighbor->getNumberOfDice() > $territory->getNumberOfDice()
                )
                {
                    continue;
                }

                return new AttackMove($territory, $neighbor);
            }
        }

        return new EndTurnMove;
    }
}