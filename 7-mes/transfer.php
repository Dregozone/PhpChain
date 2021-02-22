<?php 

    require_once 'State.php';

    $user = strtolower(getenv('USER'));
    $port = (int) $argv[1];
    $message = isset($argv[2]) ? (string) $argv[2] : '';
    
    //printf("Listening for %s on port %d\n", $user, $port);
    //if ( $peerPort ) {
    //    printf("Connecting to %d\n", $peerPort);
    //}

    printf( "Setting user $user on port $port to message $message\n" );

    (new State($user, $port))->updateTo($message);
