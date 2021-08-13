<?php 

    Class Logger 
    {
        
        
        public static function msg($message, $loggingUser) {
            
            $now = new \DateTime();
            
            if ( file_exists('data/LOG' . $loggingUser . '.txt') ) {
                $file = 'data/LOG' . $loggingUser . '.txt';
                
            } else if ( file_exists('../Communication/data/LOG' . $loggingUser . '.txt') ) {
                $file = '../Communication/data/LOG' . $loggingUser . '.txt';
                
            } else if ( file_exists('../data/LOG' . $loggingUser . '.txt') ) {
                $file = '../data/LOG' . $loggingUser . '.txt';
                
            } else if ( file_exists('Communication/data/LOG' . $loggingUser . '.txt') ) {
                $file = 'Communication/data/LOG' . $loggingUser . '.txt';
                
            } else {
                
                echo __DIR__;
            }

            if ( !file_exists($file) ) {
                // Will crerate new file for user $loggingUser
                file_put_contents($file, ''); // Create blank log txt file for this user
            }
            
            $fileManager = fopen($file, "a") or die("<br />Unable to open file from: " . getcwd());
            fwrite($fileManager, "\n" . $now->format("Y-m-d H:i:s") . " - " . $message);
            fclose($fileManager);
        }
    }
