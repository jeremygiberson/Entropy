<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 11:13 PM
 */

namespace JeremyGiberson\Entropy\Engine\Territory;

/**
 * @codeCoverageIgnore
 */
interface TerritoryInterface {
    /**
     * @return TerritoryInterface[]
     */
    public function getNeighbors();

    public function getOwner();
}