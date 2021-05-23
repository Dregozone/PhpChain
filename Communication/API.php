<?php 

    $validActions = [
        "addTransaction",
        "addDefect",
        "updateDefect",
        "getTransactions"
    ];

    // Remove bad requests
    if ( !isset($action) || !in_array($action, $validActions)  ) {
        
        return false;
    }

    // Handler functions
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
        
        if ( file_exists($file) ) {
            $data = json_decode(file_get_contents($file), true);
        } else {
            $data = [
                findPortByUser($user) => [
                    "user" => $user,
                    "data" => [],
                    "version" => 0
                ]
            ];
        }

        return $data;
    }

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

    // Prepare functions that interact with the data
    function addDefect($sn, $defectName, $user) {

        return true; // return true/false/the value being requested
    }

    function updateDefect( $sn, $defectId, $status, $user) {
        
        return true;
    }

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

        return $snTransactions; // return true/false/the value being requested
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
            if ( addDefect($sn, $defectName, $user) ) {
                
                return true;
            }

            break;

        case "updateDefect": 
    
            // Check for required variables
            if ( 
                !isset($sn) || 
                !isset($defectId) ||
                !isset($status) ||
                !isset($user)
            ) {

                return false;
            }

            // Run the command
            if ( updateDefect($sn, $defectId, $status, $user) ) {
                
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

        default:
            //
            break;
    }

    // If the actions didnt succeed, return false to let the application know if failed
    return false;
