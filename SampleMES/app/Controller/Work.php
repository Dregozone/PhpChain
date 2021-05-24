<?php 
    
    namespace app\Controller;

    Class Work extends AppController
    {
        protected $model;
        
        public function __construct($model) {
            $this->model = $model;
        }

        public function viewOperation() {

            $routings = $this->apiGetRoutings();

            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");

            $mostRecentVersion = sizeof($routings[$job]) - 1;

            if ( array_key_exists($job, $routings) && array_key_exists($operation, $routings[$job][$mostRecentVersion]) ) {
                    
                $routing = $routings[$job][$mostRecentVersion];
                $data = $routing[$operation];

                $this->model->setRouting( $routing );

                $this->model->setSequence( $data["sequence"] );
                $this->model->setName( $data["name"] );
                $this->model->setDetails( $data["details"] );

                if ( isset($_GET["sn"]) && isset($_GET["msg"]) ) { // An action has just been processed against this SN

                    $sn = $this->model->getVar("sn");
                    $msg = $this->model->getVar("msg");

                    if ( $msg == "AddedTransaction" ) {    
                        $this->model->addNotice("Successfully added transaction against SN: " . $sn . ". Job: " . $job . ", Operation: " . $operation);
                    } else if ( $msg == "AddedDefect" ) {
                        $defectName = $this->model->getVar("defectName");
                        $this->model->addNotice("Successfully added defect against SN: " . $sn . ". Defect name: " . $defectName);
                    } else if ( $msg == "UpdatedDefect" ) {
                        $defectId = $this->model->getVar("defectId");
                        $newStatus = $this->model->getVar("newStatus");
                        $this->model->addNotice("Successfully updated defect status against SN: " . $sn . ". Defect ID: " . $defectId . ", new status: " . $newStatus);
                    }
                }

            } else { // This job or operation does not exist in the available data
                // Display error message for user
                $this->model->addError("<b>Job or Operation not found!</b><br />Please use <a href=\"?p=Home\"><u>Home screen</u></a> to navigate to the operation details screen.");
            }
        }

        public function addTransaction() {
            
            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");
            $sn = $this->model->getVar("sn");

            // Use API here to add transaction: 
            $this->apiAddTransaction($sn, $job, $operation, $this->model->getUser());

            // Then redirect to clean URL for viewing the operation and able to choose a different action next
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=AddedTransaction&sn=" . $sn);
        }

        public function addDefect() {
            
            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");
            $sn = $this->model->getVar("sn");
            $defectName = $this->model->getVar("defectName");

            // Use API here to add defect: 
            $this->apiAddDefect($sn, $defectName, $this->model->getUser());

            // Then redirect to clean URL for viewing the operation and able to choose a different action next
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=AddedDefect&sn=" . $sn . "&defectName=" . $defectName);
        }

        public function findDefects() {
            
            $defects = $this->apiGetDefects();

            $this->model->setDefects( $defects );
        }

        public function updateDefect() {
            
            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");
            $sn = $this->model->getVar("sn");
            $newStatus = $this->model->getVar("newStatus");
            $defectId = $this->model->getVar("defectId");

            // Perform the defect status update using the API
            $this->apiUpdateDefect($sn, $defectId, $newStatus, $this->model->getUser());

            // Then redirect back to viewOperation at manageDefects screen
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=UpdatedDefect&sn=" . str_replace("DEFECTS", "", $sn) . "&newStatus=" . $newStatus . "&manageDefects=1&defectId=" . $defectId);
        }
    }
