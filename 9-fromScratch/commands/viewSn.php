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

    echo "<pre>";
    print_r( $output );
    echo "</pre>";
