# Entropy

![build status](https://travis-ci.org/jeremygiberson/Entropy.svg?branch=master)

Entropy is a game for programmers. How so? Well, in order to play you have to write the strategy you want to use.

Influences
  * [Dice Wars](http://www.gamedesign.jp/flash/dice/dice.html).
  * [phpwar](https://github.com/iannsp/phpwar).

## Game Play

### Game Rules
Entropy is a strategy turn based game that takes place between 2-8 players.

At the beginning of the game a random node graph is created with equal nodes assigned to each player.
Each node is connected with 4 other nodes.
Nodes can be player nodes (`Territory`) or empty nodes (`EmptyTerritory`).
Only `Territory` nodes can be owned by a player.

During a turn the player may attack another territory not under their control from an adjacent territory that is under their control and has more than one dice.
During an attack a random number is generated for the attacking and defending territories based on their respective dice count.
An attack is considered successful if the attacker random number is greater than the defender's random number.

A successful attack results in:
  * the defending territory comes under the players control
  * the defending territory's dice count is set to the attacking territories dice count - 1
  * the attacking territory's dice count is set to 1

A failed attack results in:
  * the attacking territory's dice count is set to 1

The turn comes to an end when the player no longer have any territories with more than 1 die or if the player prematurely chooses to end their turn.
A player is not required to attack during their turn.

At the end of a turn:
  * Any player that no longer occupies any territories will be disqualified
  * remaining players will gain X new dice randomly distributed to their territories, where X is the number of territories they hold.
    * A territory can only have 8 die at max. If all a player's territories are maxed out on dice then they will receive no new die.

The game is over when one player controls all territories or the specified round limit has been reached.

### Writing a Strategy
First thing you need to do is write a strategy, a strategy is a class that implements `StrategyInterface`.

```php
// a simple strategy that does nothing
class YourStrategy implements StrategyInterface {
    public function getMove($round, $player, $territories)
    {
        return new EndTurnMove();
    }
}
```

During a player turn, the player's strategy `getMove` method will be repeatedly called until it returns an instance of `EndTurnMove`.
So your strategy is potentially called multiple times per turn. Checkout the `GteStrategy` implementation for a more interesting example of a strategy.
The "Greater Than or Equal Strategy" simply attacks any adjacent enemy territory where the player territory has as many or more dice than the enemy territory.


### Running a game
In order to play:
  1. instantiate a new Game
  2. add a couple of players (specifying the strategy the player should use)
  3. play (specifying max number of rounds in case of stale mate)

It looks a bit like this (taken from bin/prototype.php)

```php
$game = new Game();
$game->addPlayer(new Player('grey', new NullStrategy()));
$game->addPlayer(new Player('red', new GteStrategy()));
$game->addPlayer(new Player('blue', new YourStrategy())); // give a player your new strategy
$game->play(100);

echo $game->getScoreboard()->render();
```


