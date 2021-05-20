<?php 

    // If user is logging out, log them out
    if ( isset($_GET["logout"]) && $_GET["logout"] == 1 ) {
        
        unset($_SESSION["username"]);
        header("location: ?p=login");
    }

    echo $view->loginArea();
