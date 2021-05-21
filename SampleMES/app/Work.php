<?php 

    // These actions are allowed from this page... 
    $validActions = [
        "viewOperation", 
        "addTransaction",
        "addDefect",
        "updateDefect"
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
            // No action has been specified, the user instead manually navigated to Work page
            $model->addError("<b>No Job or Operation selected!</b><br />Please use <a href=\"?p=Home\"><u>Home screen</u></a> to navigate to the operation details screen.");
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

            echo $view->notices();
            echo $view->showWorkScreen();

        echo $view->endContainer();
        
    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
