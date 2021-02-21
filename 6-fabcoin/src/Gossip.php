<?php 

    Class Gossip 
    {
        private $name;
        private $key;
        private $state;
        private $port;
        
        public function __construct( string $name, int $port, ?int $peerPort ) {
            
            $this->name = $name;
            $this->port = $port;
            $this->key = new Key($name);
            $peers = [$port => true];
            if ( !$peerPort ) {
                $blockchain = new Blockchain($this->key->pubKey, $this->key->privKey                     );
            } else {
                $blockchain = null;
                $peers[$peerPort] = true;
            }
            $this->state = new State($name, $blockchain, $peers);
        }
        
        public function loop() {
            
            while(true) {
                print "\x1b[100A\x1b[0J\033[37;40m Network \033[39;49m\n";
                foreach ( array_keys($this->state->peers) as $port ) {
                    if ( $this->port == $port ) {
                        continue;
                    }
                    printf(" Gossiping with %d\n", $port);
                    $this->withPeer($port);
                }
                $this->displayState();
                $this->state->reload();
                usleep(rand(300000, 3000000));
            }
        }
        
        public function withPeer($port) {
            //
        }
        
        
    }
