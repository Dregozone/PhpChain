<?php 
    
    namespace app\Controller;

    Class Work 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }

        public function viewOperation() {

            var_dump( $this->model->getVars() );
            
            ////

        }

        public function addTransaction() {
            //
        }

        public function addDefect() {
            //
        }

        public function updateDefect() {
            //
        }
    }
