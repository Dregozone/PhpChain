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
            
            $this->file = 'data/'.$user.'.json';
            
            if ($this->peerPort && !isset($this->state[$this->peerPort])) {
                $this->state[$this->peerPort] = ['user' => '', 'session' => '', 'version' => 0];
            }
            
            if ($this->port && !isset($this->state[$this->port])) {
                $this->state[$this->port] = ['user' => $user, 'session' => '', 'version' => 0];
            }
            
            $this->save();
            $this->reload();
        }

        public function loop() {

            $hasChanged = true;
            
            while(true) {
                
                if ( $hasChanged ) {
                    printf("\033[37;40m Current state \033[39;49m\n%s\n", $this);
                    $hasChanged = false;
                }
                
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
                    
                    // If there is a peer, check my values against theirs
                    if ( !$peerState ) {
                        // There is no peer, skip
                        unset($this->state[$p]);
                        $this->save();
                        
                    } else {
                        
                        $decodedPeerState = json_decode($peerState, true);
                        
                        foreach ( $decodedPeerState as $port => $data ) {
                            if ( 
                                !isset($this->state[$port]) || 
                                ( $this->state[$port]['version'] < $data['version'] ) || 
                                ( $this->state[$port]['user'] == "" && $data['user'] != "" ) 
                            ) { // If the port doesnt exist in my data, or the data for this port is incomplete, or peer version is higher
                                
                                //Logger::logMsg("Copying peer data from " . $data['user'] . " to " . $this->state[$port]['user'] . "", $this->user);
                                printf("\nCopying peers data to mine...\nMy version: %d, Peer version: %d\n\n", $this->state[$port]['version'], $data['version']);
                                Logger::logMsg("Copying peer data from x to y", $this->user);
                                
                                // Copy my peers data to my own state
                                $this->state[$port]['user'] = $data['user'];
                                $this->state[$port]['session'] = $data['session'];
                                $this->state[$port]['version'] = $data['version'];
                                
                                $hasChanged = true;
                                
                            } else {
                                // Nothing copied
                                //printf("\nNothing copied.");
                            }
                        }
                        
                        if ( $hasChanged ) {
                            $this->save();
                        }
                        
                        //$this->save();//
                        
                        $this->update(json_decode($peerState, true));
                    }             
                    
                    /*
                    if (!$peerState) {
                        unset($this->state[$p]);
                        $this->save();
                        
                    } else {
                        $this->update(json_decode($peerState, true));
                    }
                    */
                }
                
                $this->reload();
                
                usleep(rand(600000, 6000000)); // this is 2x the original currently (300000, 3000000) = orig
            }
        }

        public function reload() {

            $this->state = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        }

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
                $data[] = sprintf('%s (%s) - Version: %d', $d['user'], $port, $d['version']);
            }
            
            return implode("\n", $data);
        }

        public function incrementVersion() {
            
            // Find current version
            if ( array_key_exists($this->port, $this->state) ) {
                $version = $this->state[$this->port]['version'];
            } else {
                $version = 0;
                $this->state[$this->port]['version'] = $version;
            }
            
            // Increment to new version
            $version++;
            
            return $version;
        }

        public function save() {
            
            if (file_exists($this->file)) {
                $states = json_decode(file_get_contents($this->file), true);
                
                if ( is_array($this->state) ) {
                    
                    foreach ( $this->state as $port => $data ) { // Why is this throwing an error ////
                        $states[$port] = $data;
                    }
                }
                
            } else {
                $states = $this->state; // Default create new states list
            }
            
            file_put_contents($this->file, json_encode($states));
        }
    
        /*
        public static function updateValue( $user, $port, $value ) {
            
            $file = __DIR__.'/data/'.$user.'.json';
            
            $state = json_decode(file_get_contents($file), true);
            
            $oldVersion = $state[(int)$port]['version']; //Get current version
            $newVersion = $curVersion = $oldVersion;
            $newVersion++; // Increment version for this change
            
            // Sometimes the transactions would get lost in transmissions, implemented a rough loop to validate update was made before moving on
            while ( $curVersion == $oldVersion ) { // While the update has not changed
                
                //printf("Updating %s on port %d to value %s (v %d to v %d) [Currently v %d]\n", $user, $port, $value, $oldVersion, $newVersion, $curVersion);
                
                $state = json_decode(file_get_contents($file), true);
                
                $curVersion = $state[(int)$port]['version']; //Get current version              
                
                $state[(int)$port] = ['user' => $user, 'session' => $value, 'version' => $newVersion];
            
                if (file_exists($file)) {
                    $states = json_decode(file_get_contents($file), true);

                    foreach ( $state as $ports => $data ) {
                        $states[$ports] = $data;
                    }

                } else {
                    $states = $state; // Default create new states list
                }

                file_put_contents($file, json_encode($states));
            }
            
            return true;
        }
        */
    }
