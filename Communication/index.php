<?php 

    require_once 'classes/Logger.php';
    require_once 'classes/State.php';

    if ( '/gossip' == $_SERVER['PATH_INFO'] && 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        
        if ( isset($_ENV["USER"]) ) {
            $state = new State(strtolower($_ENV['USER']));
        } else {
            $state = new State(strtolower(getenv('USER')));
        }
        
        $state->update(json_decode(file_get_contents('php://input'), true));
        
        print json_encode($state->state);
    }
