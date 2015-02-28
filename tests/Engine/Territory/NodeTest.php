<?php
/**
 * User: Jeremy
 * Date: 2/27/2015
 * Time: 11:55 PM
 */

namespace Entropy\Tests\Engine\Territory;


use JeremyGiberson\Entropy\Engine\Territory\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function test_neighbor_accessors()
    {
        $node_a = new Node();
        $this->assertEmpty($node_a->getNeighbors(),
            'getter should return constructed value');

        $node_b = new Node();
        $node_a->addNeighbor($node_b);
        $this->assertEquals([$node_b], $node_a->getNeighbors(),
            'getter should return added node');

        $this->assertEquals([$node_a], $node_b->getNeighbors(),
            'add should add inverse neighbor relationship');
    }
}