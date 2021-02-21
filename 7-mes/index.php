<?php 

    require_once 'State.php';

    if ( '/gossip' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $state = new State(strtolower(getenv('USER')));
        $state->update(json_decode(file_get_contents('php://input'), true));
        print json_encode($state->state);
    }
