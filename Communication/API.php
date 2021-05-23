<?php 

    $validActions = [
        "addTransaction",
        "addDefect",
        "updateDefect",
        "getTransactions",
        "getJobBySn",
        "getRoutings",
        "getDefects",
        "updateRouting"
    ];

    // Remove bad requests
    if ( !isset($action) || !in_array($action, $validActions)  ) {
        
        return false;
    }

    // Handler functions
    if (!function_exists('findPortByUser')) {
        /** Return the port number of an existing user, otherwise false to say user does not exist
         * 
         *  @param string $user, The username of the user to be checked
         * 
         *  @return int $port, This user's port number
         */
        function findPortByUser($user) {

            if ( file_exists("../Communication/data/".$user.".port") ) { // If the user has been on the network before
                $port = (int)file_get_contents("../Communication/data/".$user.".port"); // Grab the port for this user
            } else {
                $port = false; // User not found!
            }

            return $port;
        }
    }

    if (!function_exists('loadFromFile')) {
        /** Load data from this user's file
         * 
         *  If file exists, pull the data locally.
         *  Else, create an empty one following this standard, empty layout.
         * 
         *  @param string $file, Filename & location to load data from
         *  @param string $user (Optional), In case of creating new, pass initialisation variables too
         *  
         *  @return array $data, Contents of file in a single array 
         */
        function loadFromFile($file, $user=null) {
            
            if ( file_exists($file) ) { // File exists
                $data = json_decode(file_get_contents($file), true);
            } else { // You are creating the file for the first time here
                $data = [
                    findPortByUser($user) => [
                        "user" => $user,
                        "data" => [
                            "ROUTING001" => [
                                "Initialisation" => [ "sequence" => 0, "name" => "Initialisation", "details" => "Scan here to initialise a new SN into this routing." ],
                                "Op 1" => [ "sequence" => 1, "name" => "Op 1", "details" => "Op 1 info." ],
                                "Op 2" => [ "sequence" => 2, "name" => "Op 2", "details" => "Op 2 info." ],
                                "Op 3" => [ "sequence" => 3, "name" => "Op 3", "details" => "Op 3 info." ],
                            ]
                        ],
                        "version" => 0,
                        "publicKey" => ""
                    ]
                ];
            }

            return $data;
        }
    }

    if (!function_exists('saveToFile')) {
        /** Save data back to file, overwriting its original contents
         * 
         *  @param string $file, Filename & location to load data from
         *  @param array $data, Array of data to overwrite the original contents with
         * 
         *  @return none
         */
        function saveToFile($file, $data) {
            file_put_contents($file, json_encode($data));
        }
    }

    if (!function_exists('addTransaction')) {
        // Prepare command functions that interact with the data
        function addTransaction($sn, $job, $operation, $user, $now) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            //var_dump($data);

            // Perform some actions on the data
            if ( array_key_exists($sn, $data[$port]["data"]) ) {
                // SN has been initialised and found
                
                // Get the existing blockchain ////
                $blockchain = $data[$port]["data"][$sn];

                // Add block to blockchain ////
                $blockchain[] = ["job" => $job, "operation" => $operation, "user" => $user, "datetime" => $now];////replace with serialised blockchain object here

            } else {
                // SN has not yet been initialised, this transaction is the first one recorded

                // Create new blockchain AND add the genesis block to it ////
                $blockchain = [["job" => $job, "operation" => $operation, "user" => $user, "datetime" => $now]];////replace with serialised blockchain object here
            }

            // Update the SN to the latest blockchain object
            $data[$port]["data"][$sn] = $blockchain;

            // Increment the version number of this port's data to indicate a change has occurred
            $data[$port]["version"]++;

            //echo "<hr />";
            //var_dump($data);

            saveToFile($file, $data);

            return true; // return true/false/the value being requested
        }
    }

    if (!function_exists('addDefect')) {
        // Prepare command functions that interact with the data
        function addDefect($sn, $defectName, $user, $now) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            // Perform some actions on the data
            if ( array_key_exists("Defect" . $sn, $data[$port]["data"]) ) {
                // SN has been initialised and found defects already recorded
                
                // Get the existing blockchain ////
                $blockchain = $data[$port]["data"]["Defect" . $sn];

                // Add block to blockchain ////
                $blockchain[] = [ "defectID" => sizeof( $data[$port]["data"]["Defect" . $sn] ), "defectName" => $defectName, "status" => "Defective", "user" => $user, "datetime" => $now, "version" => 1 ];////replace with serialised blockchain object here

            } else {
                // SN has not yet had defects recorded

                // Create new blockchain AND add the genesis block to it ////
                $blockchain = [["defectID" => 0, "defectName" => $defectName, "status" => "Defective", "user" => $user, "datetime" => $now, "version" => 1]];////replace with serialised blockchain object here
            }

            // Update the SN to the latest blockchain object
            $data[$port]["data"]["Defect" . $sn] = $blockchain;

            // Increment the version number of this port's data to indicate a change has occurred
            $data[$port]["version"]++;

            saveToFile($file, $data);

            return true; // return true/false/the value being requested
        }
    }

    if (!function_exists('updateDefect')) {
        function updateDefect( $sn, $defectId, $status, $user, $now) {
            
            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            // Perform some actions on the data
            if ( array_key_exists("Defect" . $sn, $data[$port]["data"]) ) {
                // This is a good situation, defect exists and therefore can be modified... 

                $blockchain = $data[$port]["data"]["Defect" . $sn];

                foreach ( $blockchain as $block ) {
                    
                    if ( $defectId == $block["defectID"] ) {
                        // This is the defect being updated, store its current values
                        $copyOfOriginal = $block;
                    }
                }

                $defectName = $copyOfOriginal["defectName"];
                $newVersion = $copyOfOriginal["version"] + 1;

                // Add block to blockchain ////
                $blockchain[] = [ "defectID" => $defectId, "defectName" => $defectName, "status" => $status, "user" => $user, "datetime" => $now, "version" => $newVersion ];////replace with serialised blockchain object here

            } else {
                // SN has not yet had defects recorded
                die("Something went wrong! You are attempting to update the status of a defect that doesnt exist. Please press \"back\" in your browser to continue.");
            }

            // Update the SN to the latest blockchain object
            $data[$port]["data"]["Defect" . $sn] = $blockchain;

            // Increment the version number of this port's data to indicate a change has occurred
            $data[$port]["version"]++;

            saveToFile($file, $data);

            return true; // return true/false/the value being requested










            echo "updating defect...";
            die();

            return true;
        }
    }

    if (!function_exists('updateRouting')) {
        function updateRouting( $updatedRouting, $user, $now) {
            
            echo "updating routing...";
            die();

            return true;
        }
    }

    if (!function_exists('getTransactions')) {
        // Prepare command functions that interact with the data
        function getTransactions($user) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            // Filter to only SN transactions
            $snTransactions = [];
            foreach ( $data[findPortByUser($user)]["data"] as $index => $record ) {
                
                if ( strtoupper(substr($index, 0, 6)) == "DEFECT" ) {
                    // This is a Defect record, skip
                } else if ( strtoupper(substr($index, 0, 7)) == "ROUTING" ) {
                    // This is a routing record, skip
                } else {
                    // This is a SN record, add to array to be returned
                    $snTransactions[$index] = $record;
                }
            }

            // ?? Remove the "UNDO-" operation records from the view.. they are however kept in the data
            //var_dump( $snTransactions );
            //die();

            return $snTransactions; // return true/false/the value being requested
        }
    }

    if (!function_exists('getDefects')) {
        // Prepare command functions that interact with the data
        function getDefects($user) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            // Filter to only SN defects
            $snDefects = [];
            foreach ( $data[findPortByUser($user)]["data"] as $index => $record ) {
                
                if ( strtoupper(substr($index, 0, 6)) == "DEFECT" ) {
                    // This is a Defect record, add to array to be returned
                    $snDefects[str_replace("Defect", "", $index)] = $record;
                } else if ( strtoupper(substr($index, 0, 7)) == "ROUTING" ) {
                    // This is a routing record, skip
                } else {
                    // This is a SN record, skip
                }
            }

            return $snDefects; // return true/false/the value being requested
        }
    }

    if (!function_exists('getJobBySn')) {
        // Prepare command functions that interact with the data
        function getJobBySn($user, $sn) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            if ( array_key_exists($sn, $data[findPortByUser($user)]["data"]) && array_key_exists(0, $data[findPortByUser($user)]["data"][$sn]) ) { // There is at least 1 transaction against this SN
                
                $snTransactions = $data[findPortByUser($user)]["data"][$sn][0]["job"];

            } else { // No transactions exist under this SN

                return false;
            }

            return $snTransactions; // return true/false/the value being requested
        }
    }

    if (!function_exists('getRoutings')) {
        // Prepare command functions that interact with the data
        function getRoutings($user) {

            $file = "../Communication/data/{$user}.json";
            $port = findPortByUser($user);

            $data = loadFromFile($file, $user);

            // Filter to only routings
            $routings = [];
            foreach ( $data[findPortByUser($user)]["data"] as $index => $record ) {
                
                if ( strtoupper(substr($index, 0, 6)) == "DEFECT" ) {
                    // This is a Defect record, skip
                } else if ( strtoupper(substr($index, 0, 7)) == "ROUTING" ) {
                    // This is a routing record, add to array to be returned
                    $routings[$index] = $record;
                } else {
                    // This is a SN record, skip
                }
            }

            return $routings; // return true/false/the value being requested
        }
    }

    // Process API actions
    switch( $action ) {
        case "addTransaction": 
            
            // Check for required variables
            if ( 
                !isset($sn) || 
                !isset($job) || 
                !isset($operation) || 
                !isset($user) ||
                !isset($now)
            ) {

                return false;
            }

            // Run the command
            if ( addTransaction($sn, $job, $operation, $user, $now) ) {
                
                return true;
            }

            break;

        case "addDefect": 
        
            // Check for required variables
            if ( 
                !isset($sn) || 
                !isset($defectName) || 
                !isset($user)
            ) {

                return false;
            }

            // Run the command
            if ( addDefect($sn, $defectName, $user, $now) ) {
                
                return true;
            }

            break;

        case "updateDefect": 
    
            // Check for required variables
            if ( 
                !isset($sn) || 
                !isset($defectId) ||
                !isset($status) ||
                !isset($user) || 
                !isset($now)
            ) {

                return false;
            }

            // Run the command
            if ( updateDefect($sn, $defectId, $status, $user, $now) ) {
                
                return true;
            }

            break;

        case "updateRouting": 

            // Check for required variables
            if ( 
                !isset($updatedRouting) || 
                !isset($now)
            ) {

                return false;
            }

            // Run the command
            if ( updateRouting($updatedRouting, $user, $now) ) {
                
                return true;
            }

            break;

        case "getTransactions": 
        
            // Check for required variables
            if ( 
                !isset($user)
            ) {

                return false;
            }

            // Run the command
            $transactions = getTransactions($user);
            if ( $transactions !== false ) {
                
                return $transactions;
            }

            break;

        case "getDefects": 
    
            // Check for required variables
            if ( 
                !isset($user)
            ) {

                return false;
            }

            // Run the command
            $defects = getDefects($user);
            if ( $defects !== false ) {
                
                return $defects;
            }

            break;

        case "getJobBySn": 
    
            // Check for required variables
            if ( 
                !isset($user) ||
                !isset($sn)
            ) {

                return false;
            }

            // Run the command
            $job = getJobBySn($user, $sn);
            if ( $job !== false ) {
                
                return $job;
            }

            break;

        case "getRoutings": 
    
            // Check for required variables
            if ( 
                !isset($user)
            ) {

                return false;
            }

            // Run the command
            $routings = getRoutings($user);
            if ( $routings !== false ) {
                
                return $routings;
            }

            break;

        default:
            //
            break;
    }

    // If the actions didnt succeed, return false to let the application know if failed
    return false;
