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
            
            // Cleanse user input
            $routing = $_GET["routing"] ?? false;
            $operation = $_GET["operation"] ?? false;
            $sequence = $_GET["sequence"] ?? false;
            
            if ( 
                $routing === false || 
                $operation === false || 
                $sequence === false
            ) {
                
                $this->model->addError("Failed to add operation, please report this issue!");
                
                return;
            }
            
            $blockchain = unserialize($routings[$routing]);
            
            $origRouting = $blockchain->getBlockchain();
            $origRouting = $origRouting[ sizeof($origRouting) - 1 ]->getData();
            
            // Prep new routing for modifications
            $newRouting = $origRouting;
            
            // Check for uniqueness, the name MUST be unique to this routing as is used as a UID
            if ( !in_array( $operation, $origRouting ) ) { // Ensure operation being added has a unique name

                $newSequence = $sequence - 1;
                $opToAdd = [
                        $operation => [
                            "sequence" => ($newSequence),
                            "name" => $operation,
                            "details" => "..."
                        ]
                ];
                
                ////
                /*
                echo "<hr /><h2>Adding operation...</h2>";
                
                echo "<h3>Orig routing:</h3>";
                echo "<pre>";
                print_r($origRouting);
                echo "</pre>";
                */
                
                /*
                echo "<h3>To add:</h3>";
                echo "<pre>";
                print_r($opToAdd);
                echo "</pre>";
                */
                
                $found = 0;
                $prevSequence = 10; // Start at initialisation
                $freshRouting = [];
                
                foreach ( $origRouting as $origOperation => $details ) {
                    
                    if ( ($details["sequence"] > $newSequence) && $found == 0 ) {
                        // Add new operation                        
                        $opToAdd[$operation]["sequence"] = $details["sequence"] - (int)floor(($details["sequence"] - $prevSequence) / 2);
                        $freshRouting[$operation] = [
                            "sequence" => $opToAdd[$operation]["sequence"],
                            "name" => $operation,
                            "details" => $opToAdd[$operation]["details"]
                        ];
                        
                        // Add normal next operation
                        $freshRouting[$origOperation] = $details;
                        
                        $found = 1;
                    } else {
                        $prevSequence = $details["sequence"];
                        $freshRouting[$origOperation] = $details;
                    }
                    
                }
                
                /*
                echo "<h3>New routing:</h3>";
                echo "<pre>";
                print_r($freshRouting);
                echo "</pre>";
                
                die();
                */
                ////                
                
                // Add new operation BEFORE the operation provided
                
                array_splice( $newRouting, $sequence, 0, [$operation => [
                    "sequence" => ($sequence - 0.1),
                    "name" => $operation,
                    "details" => "..."
                ]]);
                
            
            } else {
                $this->model->addError("Operation name is not valid, please report this issue!");
            }
            
            /*
            echo '<hr /><pre>';
            var_dump( $newRouting );
            echo '<hr />';
            var_dump( $freshRouting );
            echo '</pre><hr />';
            die();
            */
            
            $blockchain->addBlock( new \Block( $freshRouting ) );
            
            // Persist the change using the API (include version)
            $this->apiUpdateRouting($blockchain, $routing, $this->model->getUser());            
            
            // Then redirect to clean URL for viewing the routing and able to choose a different action next
            header("location: ?p=Routings&action=editRouting&routing=" . $routing . "&msg=UpdatedRouting&do=AddOperation&operation=" . $operation); ///DO THIS////
        }

        public function removeOperation() {
            
            $routings = $this->apiGetRoutings();

            $this->model->setRoutings( $routings );

            $routingName = $this->model->getVar("routing");
            $operationToRemove = $this->model->getVar("operation");

            // Identify and remove the requested operation
            $mostRecentVersion = sizeof(unserialize($routings[$routingName])->getBlockchain()) - 1;

            $blockchain = unserialize($routings[$routingName]);

            $origRouting = $blockchain->getBlockchain();
            $origRouting = $origRouting[ sizeof($origRouting) - 1 ]->getData();

            $newRouting = $origRouting;
            unset( $newRouting[$operationToRemove] );

            $blockchain->addBlock( new \Block( $newRouting ) );

            // Persist the change using the API (include version)
            $this->apiUpdateRouting($blockchain, $routingName, $this->model->getUser());

            // Then redirect to clean URL for viewing the routing and able to choose a different action next
            $routing = $this->model->getVar("routing");
            header("location: ?p=Routings&action=editRouting&routing=" . $routing . "&msg=UpdatedRouting&do=RemoveOperation&operation=" . $operationToRemove);
        }
    }
