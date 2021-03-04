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
