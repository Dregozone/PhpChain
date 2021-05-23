# PHP Blockchain: Manufacturing Execution System

## Getting Started

**CLI:**

### Start first node on the network

~/Documents/Projects/PhpChain/BlockchainDecentralisation$ USER=central BlockchainDecentralisation/gossip.sh


### Log in a few test users

~/Documents/Projects/PhpChain/BlockchainDecentralisation$ USER=anders PEER=central BlockchainDecentralisation/gossip.sh
~/Documents/Projects/PhpChain/BlockchainDecentralisation$ USER=emma PEER=central BlockchainDecentralisation/gossip.sh

### Host the MES application

~/Documents/Projects/PhpChain$ php -S localhost:8081

### Access the MES application

http://localhost:8081/MESApplication





## All in 1 shell script to run application:

~/Documents/Projects/PhpChain/BlockchainDecentralisation$ USER=test ./run.sh
~/Documents/Projects/PhpChain/BlockchainDecentralisation$ USER=second PEER=test ./run.sh



---

From {projectDir}/Communication, run:
(testing)
php -S localhost:8081 -t ../ --Simulating application run without starting the gossip network

(full)
USER={user} (PEER={peer}) ./run.sh --Where () is optional
