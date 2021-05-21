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

        public function showRoutingScreen() {

            $routings = $this->model->getRoutings();
            $routingName = $this->model->getVar("routing");
            $routing = $routings[$routingName];
            $action = $this->model->getVar("action");

            //var_dump( $routing );

            $html = '
                <div class="mainContent">
                    
                    <div class="routingContainer">
                        <h2 style="text-align: center; margin-bottom: 2%;">Showing routing: ' . $routingName . '</h2>
            ';

            foreach ( $routing as $operationDetails ) {
                
                $html .= '
                    <div class="flex routing">
                        <div style="width: 10%;"> Sequence: ' . $operationDetails["sequence"] . ' </div>
                        <div style="width: 10%;"> Name: ' . $operationDetails["name"] . ' </div>
                        <div style="width: 60%;"> Details: ' . $operationDetails["details"] . ' </div>
                ';

                if ( $action == "editRouting" ) { // Show the edit options if Edit, otherwise dont show edit options if just viewing
                    if ( $operationDetails["name"] != "Initialisation" ) {
                        $html .= '
                            <div style="width: 20%;">
                                <a href="?p=Routings&action=removeOperation&routing=' . $routingName . '&operation=' . $operationDetails["name"] . '">
                                    <div class="btn btn-danger">Delete</div>
                                </a>
                                <a>
                                    <div class="btn btn-secondary" onclick="alert(\'Might implement later...\');">Add operation before</div>
                                </a>
                            </div>
                        ';
                    } else {
                        // Show placeholder against initialisation op for layout
                        $html .= '
                            <div style="width: 20%;">
                                Can not be removed.    
                            </div>
                        ';
                    }
                }
                        
                $html .= '
                    </div>
                ';
            }

            $html .= '
                    </div>

                </div>
            ';

            return $html;
        }

        public function showAllRoutings() {

            $html = '
                <div class="routings">
            ';

            foreach ( $this->model->getRoutings() as $name => $details ) {
                $html .= '
                    <div class="flex routing">
                        <div style="width: 33%;">' . $name . '</div>
                        <div style="width: 33%;">' . sizeof($details) . ' operations</div>
                        <div style="width: 33%;">
                            <a href="?p=Routings&action=viewRouting&routing=' . $name . '">
                                <div class="btn btn-info">
                                    View
                                </div>
                            </a>
                            
                            <a href="?p=Routings&action=editRouting&routing=' . $name . '">
                                <div class="btn btn-info">
                                    Edit
                                </div>
                            </a>
                        </div>
                    </div>
                ';
            }

            $html .= '
                </div>
            ';

            return $html;
        }
    }
