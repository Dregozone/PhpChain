<?php 
    
    namespace app\Controller;

    Class Login extends AppController
    {
        protected $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function login() {

            // Prepare the user input for validation
            $username = $_REQUEST["username"];
            //$sk = $_REQUEST["sk"];

            // Use API to check user is legit
            if ( $this->apiCheckCredentials( $username ) === false ) { // User failed the credential check
                unset($_SESSION["username"]); // Ensure user is logged out
                header("location: ?p=Login"); // Return user to login screen

            } else { // User credentials are valid
                $user = $this->model->isLoggedIn(); // Log the user in

                if ( $user !== false ) { // User exists and is logged in
                    // User is now logged in, send them to Home
                    header("location: ?p=Home");
                }
            }
        }

        public function logout() {

            // stop decentralised gossip processes here?? ////

            unset($_SESSION["username"]);
            header("location: ?p=Login");
        }
    }
