<?php 


    //$USER = $argv[1] ?? 'central';
    //$PEER = $argv[2] ?? '';

    $USER = $_GET["user"] ?? 'emma';
    $PEER = $_GET["peer"] ?? 'dz';

//shell_exec('USER=' . $USER . ' ../BlockchainDecentralisation/gossip.sh');
//die();

    //die("User=$USER, Peer=$PEER");////

    //printf("Working... user=%s, peer=%s", $USER, $PEER);
    

    echo "Starting node for user $USER";
    
    if ( $PEER == "" ) {
        $peerPort = null;
    } else {
        echo "Bootstrapping network with node $PEER";
        $peerPort = `cat data/$PEER.port`;
    }
        
    `rm -rf data/$USER.json`;
    
    $port = 8002;
    $retry = 30;
    
    /*
    while [ $retry -gt 0 ]
    do
        if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
            let retry-=1
            let port+=1
        else
            break
        fi
    done
    */

    `echo $port > data/$USER.port`;
            
    shell_exec("php -S localhost:$port > /dev/null 2>/dev/null &");
        
    putenv("USER"); // Clear old user env var
    putenv("USER=$USER"); // Then set the new one based on this instance
    $_ENV["USER"] = $USER;

    

    //echo "mid $USER " . getenv('USER');
        
    shell_exec("php gossip.php $port $peerPort $USER > /dev/null 2>/dev/null &");
