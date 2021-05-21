<?php 

    // These actions are allowed from this page... 
    $validActions = [
        "viewRouting",
        "editRouting",
        "addOperation",
        "removeOperation"
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
            // No action has been picked, show list of available routings to view and edit
            $model->addWarning("No routing selected, please choose a routing to view/edit.");
            $controller->findRoutings();
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

            echo $view->errors();
            echo $view->warnings();
            if ( sizeof( $model->getErrors() ) > 0 ) { die(); } // If there are errors, display them but dont display the remaining view

            if ( sizeof( $model->getWarnings() ) > 0 ) {  // If there are warnings, display them and a list of valid routings to choose from 
                echo $view->showAllRoutings();
                die();
            }

            echo $view->notices();
            echo $view->showRoutingScreen();

        echo $view->endContainer();
        
    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
