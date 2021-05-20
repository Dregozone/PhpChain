<?php 
    
    namespace app\Controller;

    Class Routings 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function viewRouting() {
            
            // var_dump( $this->model->getVars() );
            ////

        }

        public function addOperation() {
            //
        }

        public function removeOperation() {
            //
        }
    }
