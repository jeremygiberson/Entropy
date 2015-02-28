<?php
/**
 * User: Jeremy
 * Date: 2/28/2015
 * Time: 12:09 AM
 */

namespace Entropy\Tests\Engine\Territory;


use JeremyGiberson\Entropy\Engine\Player;
use JeremyGiberson\Entropy\Engine\Strategy\NullStrategy;
use JeremyGiberson\Entropy\Engine\Territory\Territory;

class TerritoryTest extends \PHPUnit_Framework_TestCase
{
    public function test_number_of_dice_accessors()
    {
        $territory = new Territory();
        $this->assertEquals(0, $territory->getNumberOfDice(),
            'getter should return constructed value');
        $territory->setNumberOfDice($number = rand(1,100));
        $this->assertEquals($number, $territory->getNumberOfDice(),
            'getter should return set value');
    }

    public function test_owner_accessors()
    {
        $territory = new Territory();
        $this->assertNull($territory->getOwner(),
            'getter should return constructed value');
        $territory->setOwner($p = new Player('foo', new NullStrategy()));
        $this->assertEquals($p, $territory->getOwner(),
            'getter should return set value');
    }
}
