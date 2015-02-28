<?php
/**
 * User: Jeremy
 * Date: 2/28/2015
 * Time: 12:29 AM
 */

namespace Entropy\Tests\Engine\Move;


use JeremyGiberson\Entropy\Engine\Move\AttackMove;
use JeremyGiberson\Entropy\Engine\Territory\Territory;

class AttackMoveTest extends \PHPUnit_Framework_TestCase
{
    public function test_attacker_accessors()
    {
        $attackMove = new AttackMove($a = new Territory(), new Territory());
        $this->assertEquals($a, $attackMove->getAttacker(),
            'getter should return constructed value');
    }

    public function test_defender_accessors()
    {
        $attackMove = new AttackMove(new Territory(), $d = new Territory());
        $this->assertEquals($d, $attackMove->getDefender(),
            'getter should return constructed value');
    }
}
