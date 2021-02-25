<?php 

    require_once 'State.php';

    $user = strtolower(getenv('USER'));
    $port = (int) $argv[1];
    $peerPort = isset($argv[2]) ? (int) $argv[2] : null;
    printf("Listening for %s on port %d\n", $user, $port);
    if ( $peerPort ) {
        printf("Connecting to %d\n", $peerPort);
    }
    (new State($user, $port, $peerPort))->loop();
