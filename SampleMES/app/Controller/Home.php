<?php 
    
    namespace app\Controller;

    Class Home 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }

        public function loadNextOpBySn() { //// if SN exists, do action... 
            
            $sn = $this->model->getVar("sn");

            // Find job and op by SN using API
            ////

            //// debug until API
                $job = "JobOf" . $sn;
                $operation = "OpOf" . $sn;
            ////

            // Send to work page of this SNs next required op
            header("location: ?p=Work&job=" . $job . "&operation=" . $operation . "&action=viewOperation");
        }

        public function loadJobInitialisation() { //// if job exists, do action... 

            $job = $this->model->getVar("job");

            // Send to work page of this SNs next required op
            header("location: ?p=Work&job=" . $job . "&operation=Initialisation&action=viewOperation");
        }
    }
