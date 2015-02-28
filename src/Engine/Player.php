<?php
/**
 * User: Jeremy
 * Date: 2/26/2015
 * Time: 10:52 PM
 */

namespace JeremyGiberson\Entropy\Engine;


use JeremyGiberson\Entropy\Engine\Strategy\StrategyInterface;

class Player
{
    /** @var  string */
    protected $name;
    /** @var   */
    protected $strategy;

    function __construct($name, StrategyInterface $strategy)
    {
        $this->name = $name;
        $this->strategy = $strategy;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

}