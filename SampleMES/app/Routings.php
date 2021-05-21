<?php 

    // These actions are allowed from this page... 
    $validActions = [
        ""
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

            echo "Viewing Routings page as {$model->getUser()}";

            echo $view->errors();
            echo $view->warnings();

        echo $view->endContainer();
        
    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
