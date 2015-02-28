<?php
/**
 * User: Jeremy
 * Date: 2/28/2015
 * Time: 12:42 AM
 */

namespace JeremyGiberson\Entropy\Engine\Strategy;


use JeremyGiberson\Entropy\Engine\Move\AttackMove;
use JeremyGiberson\Entropy\Engine\Move\EndTurnMove;
use JeremyGiberson\Entropy\Engine\Player;
use JeremyGiberson\Entropy\Engine\Territory\EmptyTerritory;
use JeremyGiberson\Entropy\Engine\Territory\Territory;

class GteStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function test_getMove_ends_turn_on_empty_territories()
    {
        $strategy = new GteStrategy();
        $p = new Player('', $strategy);
        $territories = [new EmptyTerritory()];
        $this->assertEquals(new EndTurnMove(),
            $strategy->getMove(1, $p, $territories),
            'strategy should end turn');
    }

    public function test_getMove_ends_turn_when_player_does_not_own_territories()
    {
        $strategy = new GteStrategy();
        $p = new Player('', $strategy);
        $territories = [$t = new Territory()];

        $this->assertEquals(new EndTurnMove(),
            $strategy->getMove(1, $p, $territories),
            'strategy should end turn');
    }

    public function test_getMove_ends_turn_when_not_enough_dice()
    {
        $strategy = new GteStrategy();
        $p = new Player('', $strategy);
        $territories = [$t = new Territory()];
        $t->setOwner($p);
        $t->setNumberOfDice(1);

        $this->assertEquals(new EndTurnMove(),
            $strategy->getMove(1, $p, $territories),
            'strategy should end turn');
    }

    public function test_getMove_ends_turn_when_no_enemy_territories_adjacent()
    {
        $strategy = new GteStrategy();
        $p = new Player('', $strategy);
        $territories = [$t = new Territory()];
        $t->setOwner($p);
        $t->setNumberOfDice(2);

        $this->assertEquals(new EndTurnMove(),
            $strategy->getMove(1, $p, $territories),
            'strategy should end turn');
    }

    public function test_getMove_attacks_when_adjacent_to_a_weak_enemy()
    {
        $strategy = new GteStrategy();
        $p = new Player('', $strategy);
        $territories = [$t = new Territory()];
        $t->addNeighbor($e = new Territory()); // not owned by player, fewer dice
        $t->setOwner($p);
        $t->setNumberOfDice(2);

        $this->assertEquals(new AttackMove($t, $e),
            $strategy->getMove(1, $p, $territories),
            'strategy should attack');
    }
}
