<?php 
    
    namespace app\Controller;

    Class Login extends AppController
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function login() {

            $user = $this->model->isLoggedIn();

            // Main 
            if ( $user !== false ) { // User exists and is logged in
                // User is now logged in, send them to Home
                header("location: ?p=Home");
            }
        }

        public function logout() {

            // stop BC processes here?? ////

            unset($_SESSION["username"]);
            header("location: ?p=Login");
        }
    }
