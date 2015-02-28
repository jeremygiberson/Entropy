<?php

use JeremyGiberson\Entropy\Engine\Game;
use JeremyGiberson\Entropy\Engine\Player;
use JeremyGiberson\Entropy\Engine\Strategy\GteStrategy;
use JeremyGiberson\Entropy\Engine\Strategy\NullStrategy;

require_once __DIR__ . '/../vendor/autoload.php';

$game = new Game();
$game->addPlayer(new Player('grey', new NullStrategy()));
$game->addPlayer(new Player('red', new GteStrategy()));
$game->addPlayer(new Player('blue', new GteStrategy()));
$game->play(100);

echo $game->getScoreboard()->render();