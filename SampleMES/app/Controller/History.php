<?php 
    
    namespace app\Controller;

    Class History 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function viewSn() {
            
            // var_dump( $this->model->getVars() );
            ////

        }

        public function undoTransaction() {
            //
        }

        public function undoDefectChange() {
            //
        }
    }
