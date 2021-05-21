<?php 

    namespace app\Model;

    Class Routings extends AppModel 
    {
        private $routings = [];

        public function __construct() {
            //
        }
        
        public function setRoutings( array $routings ) {
            $this->routings = $routings;
        }

        public function getRoutings() {

            return $this->routings;
        }
    }
