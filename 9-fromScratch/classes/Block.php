<?php 

    Class Block 
    {
        private $sequence;
        private $datetime;
        private $data;
        private $prevHash;
        private $curHash;

        public function __construct($data, $prevHash = '') {
            
            $this->datetime = $this->getDateTime();
            $this->data = $data;
            $this->prevHash = $prevHash;
            $this->curHash = $this->calculateHash();
        }

        private function getDatetime() {

            $now = new \Datetime();
            
            return $now->format("Y-m-d H:i:s");
        }

        public function calculateHash() {
            
            return hash("sha256", $this->sequence . $this->datetime . serialize($this->data) . $this->prevHash);
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
    }

    //echo "Is valid? ";
    //var_dump( $sn["001"]->isValid() );
