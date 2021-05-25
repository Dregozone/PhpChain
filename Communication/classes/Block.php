<?php 

    Class Block 
    {
        private $sequence;
        private $datetime;
        private $data;
        private $prevHash;
        private $curHash;
        private $nonce; // Random value, nothing to do with block data but can be used with Proof of work

        public function __construct($data, $prevHash = '') {
            
            $this->datetime = $this->getDateTime();
            $this->data = $data;
            $this->prevHash = $prevHash;
            $this->curHash = $this->calculateHash();
            $this->nonce = 0;
        }

        private function getDatetime() {

            $now = new \Datetime();
            
            return $now->format("Y-m-d H:i:s");
        }

        public function calculateHash() {

            // Dont hash the sequence here during checks since we dont know the sequence of the block in chain at the time of creating the block before adding it to the chain
            $stringToHash = $this->datetime . serialize($this->data) . $this->prevHash . $this->nonce;
            //echo $stringToHash . "<br />";

            return hash("sha256", $stringToHash);
        }
        
        public function mineBlock(int $difficulty) {
            
            echo "Difficulty: {$difficulty}<br />";
            
            $zeros = '';
            for ( $i=0; $i<$difficulty; $i++ ) {
                $zeros .= '0';
            }
            
            echo "Starting to mine, looking for prefix {$zeros}<br />";
            
            while ( substr($this->curHash, 0, $difficulty) !== $zeros ) {
                $this->nonce++;
                $this->curHash = $this->calculateHash();
            }
            
            echo "Block mined: {$this->curHash}<br />";
        }

        public function setPrevHash($prevHash) {
            $this->prevHash = $prevHash;
        }

        public function setCurHash($curHash) {
            $this->curHash = $curHash;
        }

        public function setSequence($sequence) {
            $this->sequence = $sequence;
        }

        public function getPrevHash() {

            return $this->prevHash;
        }

        public function getCurHash() {
            
            return $this->curHash;
        }
        
        public function getData() {

            return $this->data;
        }

        public function getInfo() {
            
            return [
                "sequence" => $this->sequence,
                "datetime" => $this->datetime,
                "data" => $this->data,
                "prevHash" => $this->prevHash,
                "curHash" => $this->curHash,
                "nonce" => $this->nonce
            ];
        }
    }
