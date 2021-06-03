<?php 

    // Require the classes
    require_once("classes/Logger.php");
    require_once("classes/Pki.php");
    require_once("classes/Block.php");
    require_once("classes/Blockchain.php");

    // Specify valid requests for this API, all others will return value false with no actions taken
    $validActions = [
        "addTransaction",
        "addDefect",
        "updateDefect",
        "getTransactions",
        "getJobBySn",
        "getRoutings",
        "getDefects",
        "updateRouting",
        "checkCredentials"
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
        function findPortByUser($dirPrefix, $user) {

            if ( file_exists("{$dirPrefix}Communication/data/".$user.".port") ) { // If the user has been on the network before
                $port = (int)file_get_contents("{$dirPrefix}Communication/data/".$user.".port"); // Grab the port for this user
            } else {
                $port = false; // User not found!
            }

            return $port;
        }
    }

    if (!function_exists('findPublicKeyByUser')) {
        /** Will look up public key value from file stored locally, this is used to log in and sign transactions but also gossip'd to ... 
         *  ... allow other users on the network to validate your transactions against your public key
         * 
         */
        function findPublicKeyByUser($dirPrefix, $user) {

            $pkFile = "{$dirPrefix}Communication/data/pk{$user}.json";

            $pk = json_decode(file_get_contents($pkFile), true);

            return $pk;
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
        function loadFromFile($dirPrefix, $file, $user=null) {
            
            if ( file_exists($file) ) { // File exists
                $data = json_decode(file_get_contents($file), true);
            } else { // You are creating the file for the first time here
                $data = [
                    findPortByUser($dirPrefix, $user) => [
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
                        "publicKey" => findPublicKeyByUser($dirPrefix, $user)
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

            foreach ( $data as $port => $dataPart ) {

                foreach ( $dataPart["data"] as $index => $value ) {

                    // Serialize ALL blockchain objects before committing to data, otherwise JSON will exclude these and not save the contents
                    if ( gettype( $value ) == "object" ) {
                        $data[$port]["data"][$index] = serialize($value);
                    }
                }
            }

            file_put_contents($file, json_encode($data));
        }
    }

    if (!function_exists('createLock')) {
        /** Creates a .lock file that ensures no updates occur mid-transaction 
         * 
         *  @param string $dirPrefix, used in Unit tests from different base directory
         *  @param string $user, this users lock file
         * 
         *  @return boolean, successfully created lock file
         */
        function createLock($dirPrefix, $user) {
            
            Logger::msg("Locking user (Application)", $user);
            
            while(!isLocked($dirPrefix, $user)) {
                $file = fopen("{$dirPrefix}Communication/data/{$user}.lock", "w");
                fclose($file);
            }
            
            sleep(1); // Wait to allow gossip to register that its locked and to finish its processes
            
            return true;
        }
    }

    if (!function_exists('removeLock')) {
        /** Deletes the users .lock file that ensures no updates occur mid-transaction 
         * 
         *  @param string $dirPrefix, used in Unit tests from different base directory
         *  @param string $user, this users lock file
         * 
         *  @return boolean, successfully deleted the lock file
         */
        function removeLock($dirPrefix, $user) {
            
            sleep(1); // Wait before letting gossip continue
            
            Logger::msg("Un-Locking user (Application)", $user);
            
            while(isLocked($dirPrefix, $user)) {
                // Attempt to delete the lock file
                unlink("{$dirPrefix}Communication/data/{$user}.lock");
            }
            
            return true; // Return bool of whether the file still exists
        }
    }

    if (!function_exists('isLocked')) {
        /** Check whether a .lock file exists for this user 
         * 
         *  @param string $dirPrefix, used in Unit tests from different base directory
         *  @param string $user, this users lock file
         * 
         *  @return boolean, is this user locked from updates from the gossip network
         */
        function isLocked($dirPrefix, $user) {
            
            return file_exists("{$dirPrefix}Communication/data/{$user}.lock");
        }
    }

    if (!function_exists('addTransaction')) {
        // Prepare command functions that interact with the data
        function addTransaction($dirPrefix, $sn, $job, $operation, $user, $now) {

            createLock($dirPrefix, $user);
            
            Logger::msg("Adding transaction...", $user);
            
            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Perform some actions on the data
            if ( array_key_exists($sn, $data[$port]["data"]) ) {
                // SN has been initialised and found
                
                // Get the existing blockchain
                $blockchain = unserialize($data[$port]["data"][$sn]);

                // Add block to blockchain
                $blockchain->addBlock( new Block( ["job" => $job, "operation" => $operation, "user" => $user, "datetime" => $now] ) );

            } else {
                // SN has not yet been initialised, this transaction is the first one recorded

                // Create new blockchain AND add the genesis block to it
                $blockchain = new Blockchain( ["job" => $job, "operation" => $operation, "user" => $user, "datetime" => $now] );
            }

            // Update the SN to the latest blockchain object
            $data[$port]["data"][$sn] = $blockchain;

            // Increment the version number of this port's data to indicate a change has occurred
            $data[$port]["version"]++;

            saveToFile($file, $data);

            // Check the transaction was added
            $data = loadFromFile($dirPrefix, $file, $user);
            if ( array_key_exists($sn, $data[$port]["data"]) ) {
                $blockchain = unserialize($data[$port]["data"][$sn]);
            } else {
                //printf("Retrying...");
                sleep(0.25); // Wait for things to free up
                addTransaction($dirPrefix, $sn, $job, $operation, $user, $now); // Then try again
            }
            $lastBlockData = $blockchain->getLastBlock()->getData();
            $lastOpAdded = $lastBlockData["operation"];
            
            // If the op we are adding now is not equal to the last block added, then something went wrong and we need to try again
            if ( $operation != $lastOpAdded ) {
                //printf("Retrying...");
                sleep(0.25); // Wait for things to free up
                addTransaction($dirPrefix, $sn, $job, $operation, $user, $now); // Then try again
            }
            
            Logger::msg("...Transaction added", $user);
            
            removeLock($dirPrefix, $user);
            
            return true; // return true/false/the value being requested
        }
    }

    if (!function_exists('addDefect')) {
        // Prepare command functions that interact with the data
        function addDefect($dirPrefix, $sn, $defectName, $user, $now) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Perform some actions on the data
            if ( array_key_exists("Defect" . $sn, $data[$port]["data"]) ) {
                // SN has been initialised and found defects already recorded
                
                // Get the existing blockchain ////
                $blockchain = unserialize($data[$port]["data"]["Defect" . $sn]);

                // Add block to blockchain
                $blockchain->addBlock( new Block( [ "defectID" => sizeof( $blockchain->getBlockchain() ), "defectName" => $defectName, "status" => "Defective", "user" => $user, "datetime" => $now, "version" => 1 ] ) );

            } else {
                // SN has not yet had defects recorded

                // Create new blockchain AND add the genesis block to it
                $blockchain = new Blockchain( ["defectID" => 0, "defectName" => $defectName, "status" => "Defective", "user" => $user, "datetime" => $now, "version" => 1] );
            
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
        function updateDefect( $dirPrefix, $sn, $defectId, $status, $user, $now) {
            
            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Perform some actions on the data
            if ( array_key_exists("Defect" . $sn, $data[$port]["data"]) ) {
                // This is a good situation, defect exists and therefore can be modified... 

                $blockchain = unserialize($data[$port]["data"]["Defect" . $sn]);
                $bcArray = $blockchain->getBlockchain();

                foreach ( $bcArray as $block ) {
                    
                    $block = $block->getData();

                    if ( $defectId == $block["defectID"] ) {
                        // This is the defect being updated, store its current values
                        $copyOfOriginal = $block;
                    }
                }

                $defectName = $copyOfOriginal["defectName"];
                $newVersion = $copyOfOriginal["version"] + 1;

                // Add block to blockchain
                $blockchain->addBlock( new Block( [ "defectID" => $defectId, "defectName" => $defectName, "status" => $status, "user" => $user, "datetime" => $now, "version" => $newVersion ] ) );

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
        }
    }

    if (!function_exists('updateRouting')) {
        function updateRouting( $dirPrefix, $blockchain, $routingName, $user, $now) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Perform some actions on the data
            if ( array_key_exists($routingName, $data[$port]["data"]) ) {
                // This is a good situation, defect exists and therefore can be modified... 

                // Update the routing to the latest blockchain object
                $data[$port]["data"][$routingName] = $blockchain;

                // Increment the version number of this port's data to indicate a change has occurred
                $data[$port]["version"]++;

                saveToFile($file, $data);

                return true; // return true/false/the value being requested

            } else {
                // Routing does not exist
                die("Something went wrong! You are attempting to update the operation list of a routing that doesnt exist. Please press \"back\" in your browser to continue.");
            }
        }
    }

    if (!function_exists('getTransactions')) {
        // Prepare command functions that interact with the data
        function getTransactions($dirPrefix, $user) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Filter to only SN transactions
            $snTransactions = [];
            foreach ( $data[findPortByUser($dirPrefix, $user)]["data"] as $index => $record ) {
                
                if ( strtoupper(substr($index, 0, 6)) == "DEFECT" ) {
                    // This is a Defect record, skip
                } else if ( strtoupper(substr($index, 0, 7)) == "ROUTING" ) {
                    // This is a routing record, skip
                } else {
                    // This is a SN record, add to array to be returned
                    $snTransactions[$index] = $record;
                }
            }

            return $snTransactions; // return true/false/the value being requested
        }
    }

    if (!function_exists('getDefects')) {
        // Prepare command functions that interact with the data
        function getDefects($dirPrefix, $user) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Filter to only SN defects
            $snDefects = [];
            foreach ( $data[findPortByUser($dirPrefix, $user)]["data"] as $index => $record ) {
                
                if ( strtoupper(substr($index, 0, 6)) == "DEFECT" ) {
                    // This is a Defect record, add to array to be returned
                    $snDefects[str_replace("Defect", "", $index)] = unserialize($record);
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
        function getJobBySn($dirPrefix, $user, $sn) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            if ( array_key_exists($sn, $data[findPortByUser($dirPrefix, $user)]["data"]) && array_key_exists(0, unserialize($data[findPortByUser($dirPrefix, $user)]["data"][$sn])->getBlockchain()) ) { // There is at least 1 transaction against this SN

                $snTransactions = unserialize($data[findPortByUser($dirPrefix, $user)]["data"][$sn])->getBlockchain()[0]->getData()["job"];

            } else { // No transactions exist under this SN

                return false;
            }

            return $snTransactions; // return true/false/the value being requested
        }
    }

    if (!function_exists('getRoutings')) {
        // Prepare command functions that interact with the data
        function getRoutings($dirPrefix, $user) {

            $file = "{$dirPrefix}Communication/data/{$user}.json";
            $port = findPortByUser($dirPrefix, $user);

            $data = loadFromFile($dirPrefix, $file, $user);

            // Filter to only routings
            $routings = [];
            foreach ( $data[findPortByUser($dirPrefix, $user)]["data"] as $index => $record ) {
                
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

    /** Used when logging into the application
     * 
     *  1. Check whether the username has previously logged in and has a gossip'd public key value
     *        else if there is a public key stored locally,
     *        else    create a new pk/sk pair and save locally (this is the first time the user has logged in)
     *  
     *  2. Check whether a secret key file exists locally, theoretically if a SK does not exist then the user can NOT log in 
     *        Then check for validity by encrypting the username value with the user provided SK, 
     *        then decrypt the encrypted value using the publicly available public key to ensure the values match
     *            If match, log the user in
     *            Else, remain on login screen
     * 
     */
    if (!function_exists('checkCredentials')) {
        // Prepare command functions that interact with the data
        function checkCredentials($dirPrefix, $username) {

            $port = findPortByUser($dirPrefix, $username);

            $dataFile = "{$dirPrefix}Communication/data/{$username}.json";
            $data = loadFromFile($dirPrefix, $dataFile, $username);

            $pkFile = "{$dirPrefix}Communication/data/pk{$username}.json";
            $skFile = "{$dirPrefix}Communication/data/sk{$username}.json";

            $createdNew = false;
            [$sk, $pk] = Pki::generateKeyPair(); // Create a unique public/private key pair for this user


            $hasLoggedIn = false;
            // Check whether the user has previously logged in with a key pair
            foreach ( $data as $port => $contents ) {

                if ( $contents["user"] == $username && $contents["publicKey"] != "" ) { // This user has previously logged in and has a public key stored in the data
                    $pk = $contents["publicKey"];
                    $hasLoggedIn = true;
                }
            }

            if ( $hasLoggedIn ) { // There is a public key available in the data file from a previous login
                // $pk is already set
            } else if ( file_exists($pkFile) ) { // Public Key file exists locally
                $pk = json_decode(file_get_contents($pkFile), true);

            } else { // Otherwise, create new as this is the first log in
                $createdNew = true;
                file_put_contents($pkFile, json_encode($pk)); // Save for use elsewhere in the system, next login this will already exist
            }
                
            if ( $createdNew ) { // If the public key has been saved as new, also save a matching secret key for this user
                file_put_contents($skFile, json_encode($sk)); // Save for use elsewhere in the system, next login this will already exist

                return true; // This is the first time the user has logged in, newly created credentials are valid
            }

            if ( file_exists($skFile) ) { // Any user that has a valid SK stored locally can be assumed to be the genuine user
                $sk = json_decode(file_get_contents($skFile), true);

                // Encrypt the username using the available secret key
                $encrypted = Pki::encrypt($username, $sk);

                // Decrypt the encrypted username using the publicly available PK of that user to verify legitimacy
                return Pki::isValid($username, $encrypted, $pk);
            }

            return false; // If the user does not have a SK, they can not possibly be the genuine user
        }
    }

    if ( isset($isUnitTest) ) { // This is a unit test
        $dirPrefix = $isUnitTest;
    } else { // This is an application request
        $dirPrefix = "../";
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
            if ( addTransaction($dirPrefix, $sn, $job, $operation, $user, $now) ) {
                
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
            if ( addDefect($dirPrefix, $sn, $defectName, $user, $now) ) {
                
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
            if ( updateDefect($dirPrefix, $sn, $defectId, $status, $user, $now) ) {
                
                return true;
            }

            break;

        case "updateRouting": 

            // Check for required variables
            if ( 
                !isset($updatedRouting) || 
                !isset($routingName) || 
                !isset($now)
            ) {

                return false;
            }

            // Run the command
            if ( updateRouting($dirPrefix, $updatedRouting, $routingName, $user, $now) ) {
                
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
            $transactions = getTransactions($dirPrefix, $user);
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
            $defects = getDefects($dirPrefix, $user);
            if ( $defects !== false ) {
                
                return $defects;
            }

            break;

        case "checkCredentials": 

            // Check for required variables
            if ( 
                !isset($username)
            ) {

                return false;
            }

            // Run the command
            return checkCredentials($dirPrefix, $username);

        case "getJobBySn": 
    
            // Check for required variables
            if ( 
                !isset($user) ||
                !isset($sn)
            ) {

                return false;
            }

            // Run the command
            $job = getJobBySn($dirPrefix, $user, $sn);
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
            $routings = getRoutings($dirPrefix, $user);
            if ( $routings !== false ) {
                
                return $routings;
            }

            break;

        default:
            //
            break;
    }

    // If the actions didnt succeed or request is not allowed, return false to let the application know its request failed
    return false;
