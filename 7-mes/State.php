<?php 

    Class State
    {
        public $state;

        private $file;
        private $user;
        private $port;
        private $peerPort;
        private $session;

        public function __construct($user, $port=null, $peerPort=null) {

            $this->user = $user;
            $this->port = $port;
            $this->peerPort = $peerPort;
            $this->sessions = explode("\n", file_get_contents(__DIR__.'/data/sessions.txt'));
            $this->file = __DIR__.'/data/'.$user.'.json';
            if ($this->peerPort && !isset($this->state[$this->peerPort])) {
                $this->state[$this->peerPort] = ['user' => '', 'session' => '', 'version' => 0];
            }
            if ($this->port && !isset($this->state[$this->port])) {
                $this->updateMine();
            }
            $this->reload();
        }

        public function loop() {

            $i = 0;
            while(true) {
                printf("\033[37;40m Current state \033[39;49m\n%s\n", $this);
                foreach ($this->state as $p => $data) {
                    if ( $p == $this->port ) {
                        continue;
                    }
                    $data = json_encode($this->state);
                    $peerState = @file_get_contents('http://localhost:'.$p.'/gossip', null, stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-type: application/json\r\nContent-length: ".strlen($data)."\r\n",
                            'content' => $data,
                        ]
                    ]));
                    
                    var_dump( $peerState ); // This shows the values of all nodes as a dump
                    
                    if (!$peerState) {
                        unset($this->state[$p]);
                        $this->save();
                    } else {
                        $this->update(json_decode($peerState, true));
                    }
                }
                $this->reload();
                usleep(rand(300000, 3000000));
                
                /*
                if (++$i % 2) {
                    $this->updateMine();
                    printf("\033[37;40m  Favourite session updated  \033[39;49m\n");
                }
                */
            }
        }

        public function reload() {

            $this->state = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        }
        
        public function updateMine() {

            $session = $this->randomSession();
            $version = $this->incrementVersion();
            $this->state[$this->port] = ['user' => $this->user, 'session' => $session, 'version' => $version];
            $this->save();
        }
        
        //
        public function updateTo(string $value) {

            $session = $value;
            $version = $this->incrementVersion();
            $this->state[$this->port] = ['user' => $this->user, 'session' => $session, 'version' => $version];
            $this->save();
        }
        //        

        public function update($state) {

            if (!$state) {
                return;
            }
            foreach( $state as $port => $data ) {
                if ( $port == $this->port ) {
                    continue;
                }
                if ( !isset($data['user']) || !isset($data['version']) || !isset($data['session']) ) {
                    continue;
                }
                if ( !isset($this->state[$port]) || $data['version'] > $this->state[$port]['version'] ) {
                    $this->state[$port] = $data;
                }
            }
            $this->save();
        }

        public function __toString() {

            $data = [];
            foreach ( $this->state as $port => $d ) {
                $data[] = sprintf('%s/%s -- %d/%s', $port, $d['user'], $d['version'], substr($d['session'], 0, 40));
            }
            return implode("\n", $data);
        }

        // Ive created these below methods
        public function randomSession() {

            $number = rand(1, 3);

            return $this->user . $this->sessions[$number];
        }

        public function incrementVersion() {
            
            // Find current version
            $version = isset($this->state[$this->port]) ? $this->state[$this->port]['version'] : 0;
            
            // Increment to new version
            $version++;
            
            return $version;
        }

        public function save() {
            
            
            if (file_exists($this->file)) {
                $states = json_decode(file_get_contents($this->file), true);
                
                foreach ( $this->state as $port => $data ) {
                    $states[$port] = $data;
                }
                
            } else {
                $states = $this->state; // Default create new states list
            }
            
            file_put_contents($this->file, json_encode($states));
        }

    }
