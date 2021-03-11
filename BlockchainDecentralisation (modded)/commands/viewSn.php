<?php 

    require_once("../classes/Transaction.php");
    require_once("../classes/Block.php");
    require_once("../classes/Blockchain.php");
    require_once("../classes/Handler.php");

    $user = $_GET["user"] ?? null;
    $sn = $_GET["sn"] ?? null;

    $handler = new Handler($user); // Logged in user

    $handler->loadFromFile(); // Pull in this users latest values

    $output = $handler->getSn($sn);

    if ( $output !== false ) { // If the searched SN does exist
        if ( !$handler->showAllTransactions($output) ) {
            echo "<br />Failed to display all transactions.";
        }

        if ( !$handler->showLastTransaction($output) ) {
            echo "<br />Failed to display last transaction.";
        }
    } else {
        echo "<br />This SN doesnt have any transactions yet.";
    }
