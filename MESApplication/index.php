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
        
        <link rel="stylesheet" href="public/css/shared.css" />
        
        <script src="public/js/shared.js"></script>
        
        <?php 
            if ( file_exists("public/css/$page.css") ) {
                echo '<link rel="stylesheet" href="public/css/' . $page . '.css" />';
            }
            
            if ( file_exists("public/js/$page.js") ) {
                echo '<script src="public/js/' . $page . '.js"></script>';
            }
        ?>
    </head>
    <body>
    
        <main>
            <?php require "app/$page.php"; ?>
        </main>
        
    </body>
</html>
