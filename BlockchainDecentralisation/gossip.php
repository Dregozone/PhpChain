<?php 

    require_once 'classes/State.php';

    if ( isset($argv[3]) ) { // Manually setting user here
        putenv("USER=$argv[3]");
        $user = strtolower($argv[3]);
    } else {
        $user = strtolower(getenv('USER'));
    }
    
    $port = (int) $argv[1];
    $peerPort = isset($argv[2]) ? (int) $argv[2] : null;

    printf("Listening for %s on port %d\n", $user, $port);

    if ( $peerPort ) {
        printf("Connecting to %d\n", $peerPort);
    }

    (new State($user, $port, $peerPort))->loop();
