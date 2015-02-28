<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 10:25 PM
 */

namespace JeremyGiberson\Entropy\Engine\Territory;


class Node
{
    /** @var array Node[] */
    protected $neighbors = [];

    /**
     * @param Node $n
     */
    public function addNeighbor(Node $n)
    {
        $this->neighbors[] = $n;
        $n->neighbors[] = $this;
    }

    /**
     * @return array
     */
    public function getNeighbors()
    {
        return $this->neighbors;
    }
}