<?php 

    Class State
    {
        public $state;

        private $file;
        private $user;
        private $port;
        private $peerPort;
        private $initial = [];

        public function __construct($user, $port=null, $peerPort=null) {

            $this->user = $user;
            $this->port = $port;
            $this->peerPort = $peerPort;
            
            $this->initial = [
                "user" => $user,
                "data" => [
                    "ROUTING001" => serialize( new Blockchain ( [
                        "Initialisation" => [ "sequence" => 0, "name" => "Initialisation", "details" => "Scan here to initialise a new SN into this routing." ],
                        "Op 1" => [ "sequence" => 1, "name" => "Op 1", "details" => "Op 1 info." ],
                        "Op 2" => [ "sequence" => 2, "name" => "Op 2", "details" => "Op 2 info." ],
                        "Op 3" => [ "sequence" => 3, "name" => "Op 3", "details" => "Op 3 info." ],
                    ] )),
                    "ROUTING002" => serialize( new Blockchain ( [
                        "Initialisation" => [ 
                            "sequence" => 0, 
                            "name" => "Initialisation", 
                            "details" => 
                            "Scan here to initialise a new SN into this routing." 
                        ],

                        "SMT" => [ 
                            "sequence" => 1, 
                            "name" => "SMT", 
                            "details" => "Scan to record that all SMT components are fitted." 
                        ],

                        "Automated Optical Inspection" => [ 
                            "sequence" => 2, 
                            "name" => "Automated Optical Inspection", 
                            "details" => "Scan to verify AOI has completed<br />Record defects as required using the Manage Defects screen." 
                        ],

                        "Conventional Load" => [ 
                            "sequence" => 3, 
                            "name" => "Conventional Load", 
                            "details" => "Fit conventional components." 
                        ],

                        "Test" => [ 
                            "sequence" => 4, 
                            "name" => "Test", 
                            "details" => "Test IAW TWIxxx." 
                        ],

                        "Final Inspection" => [ 
                            "sequence" => 5, 
                            "name" => "Final Inspection", 
                            "details" => "Perform final inspection checks.<br />Record defects as required using the Manage Defects screen." 
                        ]
                    ] ))
                ],
                "version" => 0,
                "publicKey" => $this->findPublicKeyByUser($user)
            ];
            
            $this->file = 'data/'.$user.'.json';
            
            if ($this->peerPort && !isset($this->state[$this->peerPort])) {
                $this->state[$this->peerPort] = $this->initial;
                $this->state[$this->peerPort]["user"] = "";
            }
            
            if ($this->port && !isset($this->state[$this->port])) {
                $this->state[$this->port] = $this->initial;
            }
            
            $this->save();
            $this->reload();
        }

        private function findPublicKeyByUser($user) {

            $pkFile = "data/pk{$user}.json";

            $pk = json_decode(file_get_contents($pkFile), true);

            return $pk;
        }
        
        public function loop() {

            $hasChanged = true;
            
            while(true) {
                
                if ( $hasChanged ) {
                    $this->checkForConsensus(); // Check to see if the peer data pulled is legit, then update my own data according to majority concensus of new data
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
                                //Logger::logMsg("Copying peer data from x to y", $this->user);
                                
                                // Copy my peers data to my own state
                                $this->state[$port]['user'] = $data['user'];
                                $this->state[$port]['data'] = $data['data'];
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
        
        /** All data to be compared is currently in my own local data file and also within $this->state 
         *  at this point, compare the new data to see which parts (if any) should be pulled into my own local data file for use in the application
         *  
         *  Check for blockchain validity (isValid()), version number and data legitimacy, then copy to my own ports data values and save()
         */ 
        private function checkForConsensus() {
            
            print("\nChecking for consensus...\n");
            
            // Foreach blockchain, check isValid(), if all pass then copy the data 
            foreach ( $this->state as $port => $data ) {
                
                if ( $port == $this->port ) {
                    continue; // Skip my own port
                }
                
                // If my current version is higher than the peer version, ignore the peer value
                if ( $data["version"] <= $this->state[$this->port]["version"] ) {
                    continue;
                }
                
                // If reaching this point then the peer $data is at a higher version than my own and is not my own data
                
                // If the newer versioned, peer data contains only valid blockchain data, then copy it to my own data for use in the application
                $blockchainsAreValid = true;
                foreach ( $data["data"] as $blockchain ) {
                    // Check each individual blockchain for validity
                    if ( unserialize($blockchain)->isValid() === false ) {
                        $blockchainsAreValid = false;
                    }
                }
                
                // In the case that all blockchains have been proven to be valid, update my local values to the peers
                if ( $blockchainsAreValid ) {
                    $this->state[$this->port]["data"] = $this->state[$port]["data"];
                    $this->state[$this->port]["version"] = $this->state[$port]["version"];
                    // Blockchains are valid, peer data was copied to mine 
                    print("Blockchains are valid, peer data was copied to my own for use in the application.\n");
                } else {
                    // Checks failed, peer data ignored
                    print("Blockchains are not valid, peer data was ignored!\n");
                }
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
                
                if ( !isset($data['user']) || !isset($data['version']) || !isset($data['data']) ) {
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
