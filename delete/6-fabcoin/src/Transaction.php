<?php 

    Clas Transaction 
    {
        public $from;
        public $to; // public key of the receiver of the money
        public $amount; // amount of coins to send
        public $signature;
        
        public function __construct(?string $from, string $to, int $amount, string $privKey) {
            
            $this->from = $from;
            $this->to = $to;
            $this->amount = $amount;
            $this->signature = Pki::encrypt($this->message(), $privKey);
        }
        
        public function message() {
            
            return PoW::hash($this->from.$this->to.$this->amount);
        }
        
        public function __toString() {
            
            return ($this->from ? substr($this->from, 72, 7) : 'NONE').' -> '.substr($this->to, 72, 2).': '.$this->amount                 );
        }
        
        public function isValid() {
            
            return !$this->from || Pki::isValid($this->message(), $this->signature, $this->from);
        }
        
        
        
    }