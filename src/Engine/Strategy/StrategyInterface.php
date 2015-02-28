<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 6:25 PM
 */

namespace JeremyGiberson\Entropy\Engine\Strategy;


use JeremyGiberson\Entropy\Engine\Player;
use JeremyGiberson\Entropy\Engine\Territory\Node;
use JeremyGiberson\Entropy\Engine\Move\MoveInterface;

/**
 * @codeCoverageIgnore
 */
interface StrategyInterface
{
    /**
     * @param int $round
     * @param Player $player
     * @param Node[] $territories
     * @return MoveInterface
     */
    public function getMove($round, $player, $territories);
}