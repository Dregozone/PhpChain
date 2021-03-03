<?php 

    require_once("../classes/Transaction.php");
    require_once("../classes/Block.php");
    require_once("../classes/Blockchain.php");
    require_once("../classes/Handler.php");

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

            $handler->addTransaction($sn, $action); // SN, What

        $handler->saveToFile(); // Save back to file for gossiping

        echo "Successfully added transaction $action to SN: $sn.";

    } else {
        echo "Missing required parameters: user, sn, action";
    }
