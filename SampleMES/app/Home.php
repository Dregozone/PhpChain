<?php 

    $validActions = [
        "loadNextOpBySn",
        "loadJobInitialisation"
    ];

    // Handle logging in
    $user = $model->isLoggedIn();

    // Main 
    if ( $user !== false ) { // User exists and is logged in
        
        if ( isset($_GET["action"]) && in_array($_GET["action"], $validActions) ) {
            // Prep action
            $action = $model->cleanse($_GET["action"]);

            // Prep variables
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

            echo $view->startMain();
                echo $view->loadOperationForm();
                echo $view->loadInitialisationForm();
            echo $view->endMain();

            echo $view->errors();
            echo $view->warnings();
            
        echo $view->endContainer();

    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
