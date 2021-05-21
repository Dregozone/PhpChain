<?php 

    $validActions = [
        "login"
    ];

    // If user is logging out, log them out
    if ( isset($_GET["logout"]) && $_GET["logout"] == 1 ) {
        
        unset($_SESSION["username"]);
        header("location: ?p=Login");
    }

    // Check if user is logging in
    if ( isset($_REQUEST["action"]) && in_array($_REQUEST["action"], $validActions) ) {
        // Prep action
        $action = $model->cleanse($_REQUEST["action"]);

        foreach( $_REQUEST as $index => $value ) {
            $model->setVar($index, $value);
        }

        // Do controller action
        $controller->$action();
    }

    echo $view->loginArea();
