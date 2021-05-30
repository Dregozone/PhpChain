# PhpChain: Decentralised Manufacturing Execution System that uses Blockchain storage

This a PHP application with a custom framework that uses Blockchains (PHP objects) encoded for JSON storage locally. Then gossiped between a peer-to-peer network, using majority concensus to update from valid peer data and to communicate changes in the local user's data to the network.

## Setup

### Pre-requisites

- You need at least PHP 7.3 (CLI) plus a few common PHP extensions.
- You need to have `composer` installed.
- You need to have `Firefox` installed.
- You should be running Linux (Developed for Debian 10) to avoid encountering security issues with running the application start shell script.

### For development

1. Navigate to `{projectDir}/Communication`
2. Run `php -S localhost:8081 -t ../`. Simulating application run without starting the gossip network.

### To test using PHPUnit unit tests

1. `Navigate to  {projectDir}/`
2. Run `./vendor/bin/phpunit --testdox`. This will run each component of the system's unit tests.

### Installation and deployment usage

1. Clone the repo.
2. Run `composer install` to install dependencies.

If you are the first node on the network:
3. Run `USER={user} ./run.sh`, where {user}=Your username. This will bootstrap the gossip network with only your user to begin with.
Else
3. Run `USER={user} PEER={peer} ./run.sh`, where {user}=Your username, {peer}=A known peer username. This will join the existing gossip network looking for the named peer node.

4. Your Firefox browser will automatically open at the application login screen.

Enjoy!
