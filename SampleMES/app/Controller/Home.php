<?php 
    
    namespace app\Controller;

    Class Home 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }   
    }
