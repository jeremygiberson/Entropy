<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 10:58 PM
 */

namespace JeremyGiberson\Entropy\Factory;

use JeremyGiberson\Entropy\Engine\Territory\EmptyTerritory;
use JeremyGiberson\Entropy\Engine\Territory\Node;
use JeremyGiberson\Entropy\Engine\Territory\Territory;

class BoardFactory
{
    public function create($players)
    {
        $player_land_count = 12 - count($players);
        /** @var Node[] $open_nodes */
        $open_nodes = [];
        /** @var Node[] $closed_nodes */
        $closed_nodes = [];

        foreach($players as $player)
        {
            for($i = 0; $i < $player_land_count; $i++)
            {
                $open_nodes[] = $node = new Territory();
                $node->setOwner($player);
                $node->setNumberOfDice(rand(2,8));
            }
        }

        for($i = 0; $i < 5; $i++)
        {
            $open_nodes[] = new EmptyTerritory();
        }

        // connect territories
        while(!empty($open_nodes))
        {
            shuffle($open_nodes);
            /** @var Node $node_a */
            $node_a = array_pop($open_nodes);
            if(empty($open_nodes))
            {
                array_push($closed_nodes, $node_a);
            } else {
                /** @var Node $node_b */
                $node_b = array_pop($open_nodes);
                $node_a->addNeighbor($node_b);

                if(count($node_a->getNeighbors()) == 4)
                {
                    array_push($closed_nodes, $node_a);
                } else {
                    array_push($open_nodes, $node_a);
                }

                if(count($node_b->getNeighbors()) == 4)
                {
                    array_push($closed_nodes, $node_b);
                } else {
                    array_push($open_nodes, $node_b);
                }
            }
        }

        return $closed_nodes;
    }
}