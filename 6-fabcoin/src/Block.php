<?php 

    Class Block 
    {
        public $previous;
        public $nonce;
        public $hash;
        public $transaction;
        
        public function __construct(Transaction $transaction, ?Block $previous) {
            
            $this->previous = $previous ? $previous->hash : null;
            $this->transaction = $transaction;
            $this->mine();
        }
        
        public static function createGenesis(string $pubKey, string $privKey, int $amount) {
            return new self::(new Transaction(null, $pubKey, $amount, $privKey), null);
        }
        
        public function mine() {
            
            $data = $this->message.$this->previous;
            $this->nonce = PoW::findNonce($data);
            $this->hash = PoW::hash($data.$this->nonce);
        }
        
        public function isValid() : bool {
            
            return PoW::isValidNonce($this->message.$this->previous, $this->nonce) && $this->transaction->isValid();
        }
        
        public function __toString() : string {
            
            return sprintf("Previous: %s\nNonce: %s\nHash: %s\nMessage: %s", $this->previous, $this->nonce, $this->hash, $this->message);
        }
    }
