<?php 
    
    namespace app\View;

    Class History extends AppView
    {
        protected $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }

        public function snHistorySearchForm() {
            
            $html = '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>Enter a SN to view its history:</legend>

                            <input type="hidden" name="p" value="History" aria-label="Page selector" />
                            <input type="hidden" name="action" value="viewSn" aria-label="Action selector" />

                            <div class="flex">
                                <div class="snFormLabel">
                                    <label for="sn">Serial number: </label>
                                </div>

                                <div class="snFormInput">
                                    <input type="text" class="form-control" name="sn" id="sn" placeholder="Serial number" value="" />
                                </div>
                                
                                <div class="snFormSubmit">
                                    <input type="submit" class="btn btn-primary" value="Go" aria-label="Form submit button" />
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            ';

            return $html;
        }

        public function showHistoryScreen() {
            
            $sn = $this->model->getVar("sn");

            $html = '
                <div class="mainContent">

                    <h2 style="text-align: center;">Showing SN: ' . $sn . '</h2>

                    <div class="flex" style="align-items: flex-start">
                        <div style="width: 20%;">
                            <h3 style="text-align: center; margin-top: 23px;">Routing overview</h3>
            ';

            // Build list of completed operations
            $completedOps = [];

            foreach ( unserialize($this->model->getSnTransactions())->getBlockchain() as $transaction ) {

                $completedOps[] = $transaction->getData()["operation"];
            }

            $html .= '
                <div style="text-align: center;">
            ';

            $routing = $this->model->getRouting();

            $mostRecentVersion = sizeof( $routing ) - 1;

            if ( array_key_exists($mostRecentVersion, $routing) ) {

                foreach ( $routing[$mostRecentVersion]->getData() as $name => $details ) {                

                    $isComplete = in_array($name, $completedOps);
                    $bg = $isComplete ? 'lightgreen' : 'lightgrey';
    
                    $html .= '
                        <div class="flex operation" style="background: ' . $bg . ';">
                            <div style="width: 35%;">Sequence: ' . $details["sequence"] . '</div>
                            <div style="width: 65%;">Name: ' . $name . '</div>
                        </div>
                    ';
                }
            }

            $html .= '
                            </div>
                        </div>

                        <div style="width: 40%;">
                            <h3 style="text-align: center;">Historical transaction details</h3>

                            <div style="width: 90%; margin: 1% 5%; border: 1px solid rgba(100, 100, 100, 0.3); text-align: center;">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Operation</th>
                                            <th>User</th>
                                            <th>Time completed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
            ';

            foreach ( unserialize($this->model->getSnTransactions())->getBlockchain() as $transaction ) {
                
                $transaction = $transaction->getData();

                $html .= '
                    <tr>
                        <td>' . $transaction["operation"] . ' (' . $transaction["job"] . ')</td>
                        <td>' . $transaction["user"] . '</td>
                        <td>' . $transaction["datetime"] . '</td>
                        <td>
                ';

                if ( $transaction["operation"] != "Initialisation" && substr($transaction["operation"], 0, 5) != "UNDO-" ) {

                    $html .= '
                            <a href="?p=History&action=undoTransaction&sn=' . $sn . '&transaction=' . $transaction["operation"] . '">
                                <div class="btn btn-danger">
                                    Undo
                                </div>
                            </a>
                    ';
                } else {

                    $html .= '
                        Can not undo
                    ';
                }

                $html .= '
                        </td>
                    </tr>
                ';
            }

            $html .= '
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="width: 40%;">
                            <h3 style="text-align: center;">Defects</h3>

                            <div style="width: 90%; margin: 1% 5%; border: 1px solid rgba(100, 100, 100, 0.3); text-align: center;">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Defect ID</th>
                                            <th>Defect Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
            ';

            if ( gettype( $this->model->getSnDefects() ) != "array" ) {

                foreach ( $this->model->getSnDefects()->getBlockchain() as $defect ) {

                    $defect = $defect->getData();

                    $html .= '
                        <tr>
                            <td>' . $defect["defectID"] . '</td>
                            <td>' . $defect["defectName"] . '</td>
                            <td>' . $defect["status"] . '</td>
                        </tr>
                    ';
                }
            }

            $html .= '
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            ';

            return $html;
        }
    }
