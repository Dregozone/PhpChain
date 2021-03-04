<?php 

    require "../classes/Transaction.php";
    require "../classes/Block.php";
    require "../classes/Blockchain.php";
    require "../classes/Handler.php";

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

            $now = new \Datetime();
            $transaction = new Transaction($user, $action, $now->format("Y-m-d H:i:s"), '', 'Computer 1', '');
            $handler->addTransaction($sn, $transaction);

        $handler->saveToFile(); // Save back to file for gossiping

        echo "<br />Successfully added transaction $action to SN: $sn.";

    } else {
        echo "<br />Missing required parameters: user, sn, action";
    }
