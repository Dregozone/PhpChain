<?php 

    Class PoW 
    {
        public static function hash($message) {
            
            return hash('sha256', $message);
        }
        
        public static function findNonce($message) {
            
            $nonce = 0;
            while ( !self::isValidNonce($message, $nonce) ) {
                ++$nonce;
            }
            
            return $nonce;
        }
        
        public static function isValidNonce($message, $nonce) {
            
            // difficulty is the number of zeros we want
            return 0 === strpos(hash('sha256', $message.$nonce), '0000');
        }
    }

    Class Block 
    {
        public $previous;
        public $hash;
        public $message;
        private $datetime;
        
        public function __construct($message, ?Block $previous) {
            
            // Set time this block was added
            $this->datetime = new \Datetime();
            
            $this->previous = $previous ? $previous->hash : null;
            $this->message = $message;
            $this->mine();
        }
        
        public function mine() {
            
            $data = implode('IMPLODEJOIN', $this->message) . $this->previous;
            $this->nonce = PoW::findNonce($data);
            $this->hash = PoW::hash($data.$this->nonce);
        }
        
        public function isValid() : bool {
            
            return PoW::isValidNonce(implode('IMPLODEJOIN', $this->message) . $this->previous, $this->nonce);
        }
        
        public function __toString() : string {
            
            return sprintf("Previous: %s\nNonce: %s\nHash: %s\nMessage: User %s has completed %s on SN %s at %s", $this->previous, $this->nonce, $this->hash, $this->message['user'], $this->message['message'], $this->message['sn'], $this->datetime->format("Y-m-d H:i:s"));
        }
    }

    Class Blockchain 
    {
        public $blocks = [];
        
        public function __construct($message) {
            
            $this->blocks[] = new Block($message, null);
        }
        
        public function add($message) {
            
            $this->blocks[] = new Block($message, $this->blocks[count($this->blocks) - 1]);
            
            if ( $this->isValid() ) {
                $this->save();
            }
        }
        
        public function isValid() : bool {
            
            foreach ( $this->blocks as $i => $block ) {
                if ( !$block->isValid() ) {
                    return false;
                }
                if ( $i != 0 && $this->blocks[$i - 1]->hash != $block->previous ) {
                    return false;
                }
            }
            return true;
        }
        
        public function __toString() {
            
            return implode("\n\n", $this->blocks);
        }
        
        public function save() {
            // This is where the valid blockchain with the new block added will be stored in the "my version", ready to be gossiped
            ////
        }
    }

    // Build blockchain
    $b = new Blockchain(['sn' => 'SN001', 'message' => 'Initialisation', 'user' => '?']);
    $b->add(['sn' => 'SN001', 'message' => 'SMT S1', 'user' => '?']);
    $b->add(['sn' => 'SN001', 'message' => 'AOI S1', 'user' => '?']);

    // Validation
    print $b."\n\nIS VALID? ";
    var_export($b->isValid());
    print "\n\n";
