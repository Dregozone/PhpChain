<?php 
    
    namespace app\View;

    Class Routings extends AppView
    {
        protected $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }

    }
