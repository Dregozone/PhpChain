<?php 

    Class Blockchain 
    {
        private $data;
        private $blockchain = []; // Array of blocks becomes the blockchain

        public function __construct(Transaction $data) {
            $this->data = $data;
            $this->blockchain[] = $this->createGenesisBlock();
        }

        private function createGenesisBlock() { // Initialisation
            
            $genesis = new Block($this->data, "0");
            $genesis->setSequence(0);

            return $genesis;
        }

        public function addBlock(Block $block) {
            
            $block->setPrevHash($this->getLastBlock()->getCurHash());
            $block->setCurHash($block->calculateHash());

            $block->setSequence( sizeof( $this->blockchain ) );

            $this->blockchain[] = $block;
        }

        public function isValid() {

            for ( $i=1, $j=sizeof($this->blockchain); $i<$j; $i++ ) {
                
                $curBlock = $this->blockchain[$i];
                $prevBlock = $this->blockchain[$i-1];

                // Check that the current hash is valid to the values within the block
                if ( $curBlock->getCurHash() !== $curBlock->calculateHash() ) {

                    return false;
                }

                // Check that the chain is correctly sequenced by comparing previous hash with the previous blocks current hash
                if ( $curBlock->getPrevHash() !== $prevBlock->getCurHash() ) {
                    
                    return false;
                }
            }

            return true;
        }

        public function getLastBlock() {
            
            return $this->blockchain[(sizeof($this->blockchain)-1)];
        }

        public function getBlockchain() {

            return $this->blockchain;
        }
    }
