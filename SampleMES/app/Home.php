<?php 

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

            echo "Viewing Home page as {$model->getUser()}";

            echo '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>Load next op by SN:</legend>

                            <input type="hidden" name="p" value="Work" aria-label="Page selector" />
                            <input type="hidden" name="action" value="viewNextOperation" aria-label="Action selector" />

                            <div class="flex">
                                <div class="snFormLabel">
                                    <label for="sn">Serial number: </label>
                                </div>

                                <div class="snFormInput">
                                    <input type="text" class="form-control" name="sn" id="sn" placeholder="Serial number" value="" />
                                </div>
                                
                                <div class="snFormSubmit">
                                    <input type="submit" class="btn btn-primary" value="Go" aria-label="Form submit button" />
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            ';

        echo $view->endContainer();

    } else {
        // User is not logged in, send back to Login page
        header("location: ?p=login");
    }
