<?php 

    $validActions = [
        "login",
        "logout"
    ];

    // Check user actions for logging in/out 
    if ( isset($_REQUEST["action"]) && in_array($_REQUEST["action"], $validActions) ) {
        // Prep action
        $action = $model->cleanse($_REQUEST["action"]);

        // Prep variables 
        foreach( $_REQUEST as $index => $value ) {
            $model->setVar($index, $value);
        }

        // Do controller action
        $controller->$action();
    }

    echo $view->loginArea();

    echo $view->errors();
    echo $view->warnings();
