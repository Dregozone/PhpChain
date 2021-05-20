<?php 

    namespace app\Model;

    Class AppModel 
    {
        private $vars = [];

        /** If user is logged in, return the cleansed username, else return false and the page will not be displayed
         * 
         */
        public function isLoggedIn() {

            if ( isset($_SESSION["username"]) ) {
                
                return $_SESSION["username"];
            } else if ( isset($_POST["username"]) && true ) { //// Will later also check for credentials validity as well as "isset"

                // Log the user in with Session variable
                $_SESSION["username"] = $this->cleanse($_POST["username"]);

                return $_SESSION["username"];
            } else {
                
                return false; 
            }
        }

        public function cleanse($dirty) {

            return htmlspecialchars(trim($dirty));
        }

        public function setVar($index, $value) {

            // All SNs will only use uppercase to avoid problems later on
            if ( $index == "sn" ) {
                $value = strtoupper($value);
            }

            $this->vars[$index] = $value;
        }

        public function getVars() {

            return $this->vars;
        }

        public function getVar( $index ) {

            return $this->vars[$index];
        }

        public function getUser() {

            return $_SESSION["username"];
        }
    }
