<?php 
    
    namespace app\View;

    Class Work extends AppView
    {
        protected $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }

        private function scanForThisOperationForm() {

            $html = '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>Scan SN to complete this operation:</legend>

                            <input type="hidden" name="p" value="Work" aria-label="Page selector" />
                            <input type="hidden" name="action" value="addTransaction" aria-label="Action selector" />

                            <input type="hidden" name="job" value="' . $this->model->getVar("job") . '" aria-label="Job number" />
                            <input type="hidden" name="operation" value="' . $this->model->getVar("operation") . '" aria-label="Operation name" />

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

        private function addDefectForm() {

            $html = '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>Add a defect against a SN:</legend>

                            <input type="hidden" name="p" value="Work" aria-label="Page selector" />
                            <input type="hidden" name="action" value="addDefect" aria-label="Action selector" />

                            <input type="hidden" name="job" value="' . $this->model->getVar("job") . '" aria-label="Job number" />
                            <input type="hidden" name="operation" value="' . $this->model->getVar("operation") . '" aria-label="Operation name" />

                            <div class="flex">
                                <div class="snFormLabel">
                                    <label for="sn">Serial number: </label>
                                </div>

                                <div class="snFormInput">
                                    <input type="text" class="form-control" name="sn" id="sn" placeholder="Serial number" value="" />
                                </div>

                                <div class="snFormInput">
                                    <select name="defectName" class="form-control">
                                        <option value="Damaged part">Damaged part</option>
                                        <option value="Missing part">Missing part</option>
                                        <option value="Incorrect part">Incorrect part</option>
                                    </select>
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

        public function showWorkScreen() {

            $job = $this->model->getVar("job");
            $searchedOperation = $this->model->getVar("operation");

            $sequence = $this->model->getSequence();
            $name = $this->model->getName();
            $details = $this->model->getDetails();

            $routing = $this->model->getRouting();

            $html = '
                <div class="mainContent">
                    <div class="flex" style="text-align: center;">
                        <div style="width: 20%;"><h2>Op sequence: ' . $sequence . '</h2></div>
                        <div style="width: 60%;"><h2>' . $name . '</h2></div>
                        <div style="width: 20%;"></div>
                    </div>

                    <div class="flex" style="align-items: flex-start;">
                        <div style="width: 80%;">
            ';

            if ( isset($_GET["manageDefects"]) ) {
                
                $this->controller->findDefects();

                $defects = $this->model->getDefects();

                $html .= '
                    <div class="manageDefectsScreen">
                        <h3>Manage defects</h3>
                    
                        <div class="defects">
                ';

                foreach ( $defects as $sn => $defectSnGroup ) {

                    $html .= '
                        <div class="defectSnGroup">
                            <h4>' . $sn . '</h4>

                            <div class="flex defect defectHeader">
                                <div style="width: 20%;">Defect UD</div>
                                <div style="width: 30%;">Defect Name</div>
                                <div style="width: 20%;">Current Status</div>

                                <div style="width: 30%;">
                                    Actions
                                </div>
                            </div>
                    ';

                    foreach ( $defectSnGroup as $defect ) {
                        $html .= '
                            <div class="flex defect">
                                <div style="width: 20%;">' . $defect["defectID"] . '</div>
                                <div style="width: 30%;">' . $defect["defectName"] . '</div>
                                <div style="width: 20%;">' . $defect["status"] . '</div>

                                <div style="width: 30%;">
                                    <a href="?p=Work&action=updateDefect&sn=' . $sn . '&defectId=' . $defect["defectID"] . '&newStatus=Defective&version=' . ($defect["version"] + 1) . '&job=' . $job . '&operation=' . $searchedOperation . '"><div class="btn btn-danger">  Defective </div></a>
                                    <a href="?p=Work&action=updateDefect&sn=' . $sn . '&defectId=' . $defect["defectID"] . '&newStatus=Reworked&version='  . ($defect["version"] + 1) . '&job=' . $job . '&operation=' . $searchedOperation . '"><div class="btn btn-warning"> Reworked  </div></a>
                                    <a href="?p=Work&action=updateDefect&sn=' . $sn . '&defectId=' . $defect["defectID"] . '&newStatus=Inspected&version=' . ($defect["version"] + 1) . '&job=' . $job . '&operation=' . $searchedOperation . '"><div class="btn btn-success"> Inspected </div></a>
                                </div>

                            </div>
                        ';
                    }

                    $html .= '
                        </div>
                    ';
                }

                $html .= '
                        </div>
                    </div>
                ';

            } else {
                $html .= '
                    ' . $this->scanForThisOperationForm() . '

                    <h3>Operation Details:</h3>

                    <div style="text-align: center;">
                        ' . $details . '
                    </div>

                    ' . $this->addDefectForm() . '
                ';
            }

            $html .= '
                        </div>

                        <div style="width: 20%;">
                            <h3>Routing overview:</h3>

                            <div class="operations">
            ';
            
            $opFound = false;
            foreach ( $routing as $operation => $operationInfo ) {

                if ( $operation == $searchedOperation ) {
                    // This is the operation we're viewing
                    $opFound = true;
                    $bg = "lightblue";
                    $fontWeight = "font-weight: bold;";
                    $linkStart = '<a href="?p=Work&action=viewOperation&job=' . $job . '&operation=' . $searchedOperation . '">';
                    $linkEnd = '</a>';

                } else if ( $opFound ) {
                    // This operation is AFTER the currently viewed op
                    $bg = "lightgrey";
                    $fontWeight = '';
                    $linkStart = '';
                    $linkEnd = '';

                } else {
                    // This operation is BEFORE the currently viewed op
                    $bg = "#66ff66";
                    $fontWeight = '';
                    $linkStart = '';
                    $linkEnd = '';
                }

                $html .= '
                    ' . $linkStart . '
                        <div class="operation" style="background: ' . $bg . '; ' . $fontWeight . '">
                            ' . $operation . '
                        </div>
                    ' . $linkEnd . '
                ';
            }

            $html .= '
                            </div>

                            <div class="manageDefects">
                                <a href="?p=Work&action=viewOperation&job=' . $job . '&operation=' . $searchedOperation . '&manageDefects=1">
                                    <div class="btn btn-secondary">
                                        Manage defects
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            ';

            return $html;
        }
    }
