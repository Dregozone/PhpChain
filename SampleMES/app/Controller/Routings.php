<?php 
    
    namespace app\Controller;

    Class Routings extends AppController
    {
        protected $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function findRoutings() {

            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );
        }

        public function viewRouting() {
            
            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );
        }

        public function editRouting() { // Could later add permissions here as required ////
            // Use this action to allow the user to view the routing and choose to add or remove an operation

            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );

            if ( isset($_GET["msg"]) ) { // An action has just been processed against this SN

                $msg = $this->model->getVar("msg");
                $do = $this->model->getVar("do");
                $operation = $this->model->getVar("operation");
                $routing = $this->model->getVar("routing");

                if ( $do == "AddOperation" ) {
                    $this->model->addNotice("Successfully added operation " . $operation . " to routing: " . $routing . ".");

                } else if ( $do == "RemoveOperation" ) {

                    $this->model->addNotice("Successfully deleted operation " . $operation . " from routing: " . $routing . ".");
                }
            }
        }

        /** Will add only if I have time... this is optional and less value added
         * 
         */
        public function addOperation() {
            
            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );

            // Check for uniqueness, the name MUST be unique to this routing as is used as a UID
            ////

            // Add new operation BEFORE the operation provided
            ////

            // Persist the change using API (include version)
            ////

            // Redirect as required and show notice confirming the addition, send to editRouting page for this routing to show the change with notice
            ////
        }

        public function removeOperation() {
            
            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );

            $routingName = $this->model->getVar("routing");
            $operationToRemove = $this->model->getVar("operation");

            // Identify and remove the requested operation
            $mostRecentVersion = sizeof($routings[$routingName]) - 1;
            unset( $routings[$routingName][$mostRecentVersion][$operationToRemove] );

            // Persist the change using the API (include version)
            $this->apiUpdateRouting($routings[$routingName], $routingName, $this->model->getUser());

            // Then redirect to clean URL for viewing the routing and able to choose a different action next
            $routing = $this->model->getVar("routing");
            header("location: ?p=Routings&action=editRouting&routing=" . $routing . "&msg=UpdatedRouting&do=RemoveOperation&operation=" . $operationToRemove);
        }
    }
