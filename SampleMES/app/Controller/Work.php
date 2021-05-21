<?php 
    
    namespace app\Controller;

    Class Work 
    {
        private $model;
        
        public function __construct($model) {
            $this->model = $model;
        }

        public function viewOperation() {

            //// debug usage, later pull this info from the API 
                $routings = [
                    "ROUTING001" => [
                        "Initialisation" => [ "sequence" => 0, "name" => "Initialisation", "details" => "Scan here to initialise a new SN into this routing." ],
                        "OP1" => [ "sequence" => 1, "name" => "Op 1", "details" => "Op 1 info." ],
                        "OP2" => [ "sequence" => 2, "name" => "Op 2", "details" => "Op 2 info." ],
                        "OP3" => [ "sequence" => 3, "name" => "Op 3", "details" => "Op 3 info." ],
                    ]
                ];
            ////

            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");

            if ( array_key_exists($job, $routings) && array_key_exists($operation, $routings[$job]) ) {
                
                $routing = $routings[$job];
                $data = $routings[$job][$operation];

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
            //echo "Adding transaction against SN: {$sn}. Job: {$job}, Operation: {$operation}.";

            // Then redirect to clean URL for viewing the operation and able to choose a different action next
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=AddedTransaction&sn=" . $sn);
        }

        public function addDefect() {
            
            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");
            $sn = $this->model->getVar("sn");
            $defectName = $this->model->getVar("defectName");

            // Use API here to add defect: 
            //echo "Adding defect against SN: {$sn}. Defect name: {$defectName}.";

            // Then redirect to clean URL for viewing the operation and able to choose a different action next
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=AddedDefect&sn=" . $sn . "&defectName=" . $defectName);
        }

        public function findDefects() {
            
            //// debug usage, later get the defect list from the API
                $defects = [
                    "DefectsSN001" => [[ "defectID" => 1, "defectName" => "Missing part", "status" => "Defective", "version" => 1 ], [ "defectID" => 3, "defectName" => "Incorrect part", "status" => "Defective", "version" => 1 ]], /* 2 defects against this SN */
                    "DefectsSN002" => [[ "defectID" => 2, "defectName" => "Damaged part", "status" => "Defective", "version" => 1 ]] /* only 1 defect against this SN */
                ];
            ////

            $this->model->setDefects( $defects );
        }

        public function updateDefect() {
            
            $job = $this->model->getVar("job");
            $operation = $this->model->getVar("operation");
            $sn = $this->model->getVar("sn");
            $newStatus = $this->model->getVar("newStatus");
            $defectId = $this->model->getVar("defectId");

            // Perform the defect status update using the API
            ////

            // Then redirect back to viewOperation at manageDefects screen
            header("location: ?p=Work&action=viewOperation&job=" . $job . "&operation=" . $operation . "&msg=UpdatedDefect&sn=" . str_replace("DEFECTS", "", $sn) . "&newStatus=" . $newStatus . "&manageDefects=1&defectId=" . $defectId);
        }
    }
