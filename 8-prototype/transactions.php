<?php 
    
    require_once 'State.php';

    $user = isset($argv[1]) ? (string) $argv[1] : null;
    $operation = isset($argv[2]) ? (string) $argv[2] : null;

    $datetime = new \DateTime();

    $port = null;

    if ( file_exists("data/".$user.".port") ) { // If the user has been on the network before
        $port = file_get_contents("data/".$user.".port"); // Grab the port for this user
    }

    if ( $user && $operation && $port ) { // If entered data is good
        printf("Adding transaction -\n  Operator: %s (Port: %d)\n  Operation: %s\n  Datetime: %s\n", $user, $port, $operation, $datetime->format("Y-m-d H:i:s"));
        
    } else {
        printf("Failed to add! Use 'php transactions.php {user} {operation}'\n");
        die(); // Bad data input
    }


    State::updateValue( $user, $port, $operation );
