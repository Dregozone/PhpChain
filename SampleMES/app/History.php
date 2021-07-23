<?php 

    // These actions are allowed from this page... 
    $validActions = [
        "viewSn",
        "undoTransaction"
    ];
    
    // Handle logging in
    $user = $model->isLoggedIn();

    // Main 
    if ( $user !== false ) { // User exists and is logged in
        
        if ( isset($_GET["action"]) && in_array($_GET["action"], $validActions) ) {
            // Prep action
            $action = $model->cleanse($_GET["action"]);

            foreach( $_GET as $index => $value ) {
                $model->setVar($index, $value);
            }

            // Do controller action
            $controller->$action();
        } else {
            // Show user a form to search for a SN to view the transaction and defect details
            $model->addWarning("No SN selected, please enter a SN to view its history.");
        }
        
        echo $view->loggedInAs($user);
        echo $view->title("Sample MES - " . $model->cleanse($_GET["p"]));
        
        echo $view->startContainer($user);

            echo $view->startNav();
                echo $view->button("Home", "Home");
                echo $view->button("Work", "Work");
                echo $view->button("History", "History");
                echo $view->button("Routings", "Routings");
            echo $view->endNav();

            //echo $view->errors();
            echo $view->warnings();

            if ( sizeof( $model->getErrors() ) > 0 ) { die(); } // If there are errors, display them but dont display the remaining view

            if ( sizeof( $model->getWarnings() ) > 0 ) {  // If there are warnings, display them and a list of valid routings to choose from 
                echo $view->snHistorySearchForm();
                die();
            }

            echo $view->notices();
            echo $view->showHistoryScreen();
        
            echo $view->errors(); // Show errors later on this screen to catch issue where the SN doesnt exist

        echo $view->endContainer();
        
    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
