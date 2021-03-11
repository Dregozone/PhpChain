<?php 

    if ( isset($_GET["p"]) && file_exists("app/{$_GET["p"]}.php") ) {
        $page = htmlspecialchars($_GET["p"]);
    } else {
        $page = 'Login';
    }

    if ( 
        file_exists( "app/Model/$page.php" ) &&
        file_exists( "app/Controller/$page.php" ) &&
        file_exists( "app/View/$page.php" )
    ) { // This is an MVC page
        require_once("app/Model/$page.php");
        $modelString = "app\\Model\\$page";
        $model = new $modelString();

        require_once("app/Controller/$page.php");
        $controllerString = "app\\Controller\\$page";
        $controller = new $controllerString($model);

        require_once("app/View/$page.php");
        $viewString = "app\\View\\$page";
        $view = new $viewString($model, $controller);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $page ?> - MES Application</title>
        
        <!-- Add CSS reset/normalise here -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="MESApplication/public/css/shared.css" />
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="MESApplication/public/js/shared.js"></script>
        
        <?php 
            if ( file_exists("public/css/$page.css") ) {
                echo '<link rel="stylesheet" href="MESApplication/public/css/' . $page . '.css" />';
            }
            
            if ( file_exists("public/js/$page.js") ) {
                echo '<script src="MESApplication/public/js/' . $page . '.js"></script>';
            }
        ?>
    </head>
    <body>
    
        <main>
            <?php require "app/$page.php"; ?>
        </main>
        
    </body>
</html>
