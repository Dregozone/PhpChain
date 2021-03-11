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
