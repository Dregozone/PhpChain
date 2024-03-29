# PhpChain: Decentralised Manufacturing Execution System that uses Blockchain storage

This a PHP application with a custom framework that uses Blockchains (PHP objects) encoded for JSON storage locally. Then gossiped between a peer-to-peer network, using majority concensus to update from valid peer data and to communicate changes in the local user's data to the network.

## Setup

### Pre-requisites

- You need at least PHP 7.3 (CLI) plus a few common PHP extensions.
- You need to have `composer` installed.
- You need to have `firefox` installed (Launches Firefox by default).
- You should be running Linux (Developed for Debian 10) to avoid encountering security issues with running the application start shell script.

### Installation

1. Clone the repo: `git clone https://github.com/Dregozone/PhpChain` (Or alternative method).
2. Navigate to `PhpChain/`
3. Run `composer install` to install dependencies.

#### For development (Simulating MES application run without starting the gossip network)

1. Navigate to `PhpChain/Communication`
2. Run `php -S localhost:8081 -t ../`
3. Open browser at `localhost:8081/SampleMES`

#### To test (Using PHPUnit unit tests)

1. Navigate to `PhpChain/`
2. Run `./vendor/bin/phpunit --testdox`. This will run each component of the system's unit tests.

#### To use

1. Navigate to `PhpChain/Communication`

If you are the first node on the network:

2. Run `USER={user} ./run.sh`, where {user}=Your username. This will bootstrap the gossip network with only your user to begin with.

Else

2. Run `USER={user} PEER={peer} ./run.sh`, where {user}=Your username, {peer}=A known peer username. This will join the existing gossip network looking for the named peer node.

3. Your Firefox browser will automatically open at the application login screen.

Enjoy!
