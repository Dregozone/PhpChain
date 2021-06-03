<?php 

    Class Logger 
    {
        
        
        public static function msg($message, $loggingUser) {
            
            $now = new \DateTime();
            
            if ( file_exists('data/LOG' . $loggingUser . '.txt') ) {
                $file = 'data/LOG' . $loggingUser . '.txt';
                
            } else if ( file_exists('../Communication/data/LOG' . $loggingUser . '.txt') ) {
                $file = '../Communication/data/LOG' . $loggingUser . '.txt';
                
            } /* else if ( file_exists('../../Communication/data/LOG' . $loggingUser . '.txt') ) {
                $file = '../../Communication/data/LOG' . $loggingUser . '.txt';
            }*/

            $fileManager = fopen($file, "a") or die("Unable to open file from: " . getcwd());
            fwrite($fileManager, "\n" . $now->format("Y-m-d H:i:s") . " - " . $message);
            fclose($fileManager);
            
            //shell_exec('echo "\n' . $now->format("Y-m-d H:i:s") . ' - ' . $message . '" >> ' . $file);                  
        }
    }
