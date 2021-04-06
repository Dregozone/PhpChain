<?php 

    Class Logger 
    {
        
        
        public static function logMsg($message, $loggingUser) {
            
            
            
            $now = new \DateTime();
            
            if ( file_exists('../data/LOG' . $loggingUser . '.txt') ) {            
                shell_exec('echo "\n' . $now->format("Y-m-d H:i:s") . ' [1] - ' . $message . '" >> ../data/LOG' . $loggingUser . '.txt');
    
            } else if ( file_exists('../../BlockchainDecentralisation/data/LOG' . $loggingUser . '.txt') ) {
                shell_exec('echo "\n' . $now->format("Y-m-d H:i:s") . ' [2] - ' . $message . '" >> ../../BlockchainDecentralisation/data/LOG' . $loggingUser . '.txt');
                
            }            
        }
    }
