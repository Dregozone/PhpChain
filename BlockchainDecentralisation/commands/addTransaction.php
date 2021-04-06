<?php 

    require "../classes/Transaction.php";
    require "../classes/Block.php";
    require "../classes/Blockchain.php";
    require "../classes/Handler.php";
    require "../classes/Logger.php";

    $user = $_GET["user"] ?? null;
    $sn = $_GET["sn"] ?? null;
    $action = $_GET["action"] ?? null;

    if ( 
        $user != null &&
        $sn != null &&
        $action != null
     ) {
        $handler = new Handler($user); // Logged in user

        $handler->loadFromFile(); // Pull in this users latest values

            $originalSize = $handler->getBlockchainLength($sn);
        
            $now = new \Datetime();
            $transaction = new Transaction($user, $action, $now->format("Y-m-d H:i:s"), '', 'Computer 1', '');
            $handler->addTransaction($sn, $transaction);

        $handler->saveToFile(); // Save back to file for gossiping
        
        // Wait for gossip to complete
        usleep( 6000000 ); /* rand(3000000, 30000000) */
        
        $handler->loadFromFile();
        while ( $originalSize == $handler->getBlockchainLength($sn) ) { // While the new block is not added
            
            // Redo until genuinely added
            $handler->loadFromFile(); // Pull in this users latest values

                $now = new \Datetime();
                $transaction = new Transaction($user, $action, $now->format("Y-m-d H:i:s"), '', 'Computer 1', '');
                $handler->addTransaction($sn, $transaction);

            $handler->saveToFile(); // Save back to file for gossiping
            
            // Wait for gossip to complete
            usleep( 6000000 ); /* rand(3000000, 30000000) */
            
            echo "<br />...Trying again...";
            Logger::logMsg("Trying again to add transaction...", $user);
        }
        
        echo "<br />Successfully added transaction $action to SN: $sn.";

    } else {
        echo "<br />Missing required parameters: user, sn, action";
    }
