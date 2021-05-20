<?php 

    // Handle logging in
    $user = $_POST["username"] ?? false;

    // Main 
    if ( $user !== false ) { // User exists and is logged in
        
        echo $view->loggedInAs($user);
        echo $view->title("PhpChain MES");
        
        echo $view->startContainer($user);
            echo $view->snSearch();
            echo $view->addTransaction();
            echo $view->snResults();
            echo $view->addTransactionResults();
        echo $view->endContainer();
    }
