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
            $routing = unserialize($routings[$routingName])->getBlockchain();
            $action = $this->model->getVar("action");

            $mostRecentVersion = sizeof($routing) - 1;
            $routing = $routing[$mostRecentVersion]->getData();

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
                            
                                <div>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        style="display: inline-block; width: 79%;" 
                                        name="opName" 
                                        id="opName' . $operationDetails["sequence"] . '" 
                                        placeholder="Add operation before" 
                                        aria-label="Operation name" 
                                    />
                                    
                                    <a>
                                        <div class="btn btn-success" style="width: 14%;" onclick="addOperation(\'' . $operationDetails["sequence"] . '\', \'' . $routingName . '\');">
                                            +
                                        </div>
                                    </a>
                                </div>
                                
                                <div>
                                    <a href="?p=Routings&action=removeOperation&routing=' . $routingName . '&operation=' . $operationDetails["name"] . '">
                                        <div class="btn btn-danger" style="width: 95%; margin-top: 0.5%;">Delete this operation</div>
                                    </a>
                                </div>
                                
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

                    <div class="routingContainer">
                        <br /><hr />
                        <h3>Routing history <small><i>(Most recent version at the top)</i></small></h3>
            ';

            $currentRouting = unserialize($routings[$routingName])->getBlockchain();
            $versionedRoutings = array_reverse($currentRouting, true);

            foreach ( $versionedRoutings as $version => $data ) {

                $data = $data->getData();

                $html .= '
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Version</th>
                                <th>Sequence</th>
                                <th>Operation</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                ';

                foreach ( $data as $opName => $attribute ) {
                    
                    $html .= '
                        <tr>
                            <td>' . $version . '</td>
                            <td>' . $attribute["sequence"] . '</td>
                            <td>' . $attribute["name"] . '</td>
                            <td>' . $attribute["details"] . '</td>
                        </tr>
                    ';
                    
                }
                
                $html .= '
                        </tbody>
                    </table>
                    <br /> <!-- For spacing between routing versions --> 
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

                $details = unserialize($details)->getBlockchain();

                $mostRecentVersion = sizeof($details) - 1;

                $html .= '
                    <div class="flex routing">
                        <div style="width: 33%;">' . $name . '</div>
                        <div style="width: 33%;">' . sizeof($details[$mostRecentVersion]->getData()) . ' operation(s)</div>
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
