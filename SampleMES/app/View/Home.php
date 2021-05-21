<?php 
    
    namespace app\View;

    Class Home extends AppView
    {
        protected $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }

        public function loadOperationForm() {

            $html = '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>View SNs next operation:</legend>

                            <input type="hidden" name="p" value="Home" aria-label="Page selector" />
                            <input type="hidden" name="action" value="loadNextOpBySn" aria-label="Action selector" />

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

        public function loadInitialisationForm() {

            $html = '
                <div class="snForm">
                    <form action="#" method="GET" autocomplete="off">
                        <fieldset>
                            <legend>Initialise SNs into job:</legend>

                            <input type="hidden" name="p" value="Home" aria-label="Page selector" />
                            <input type="hidden" name="action" value="loadJobInitialisation" aria-label="Action selector" />

                            <div class="flex">
                                <div class="snFormLabel">
                                    <label for="job">Job number: </label>
                                </div>

                                <div class="snFormInput">
                                    <input type="text" class="form-control" name="job" id="job" placeholder="Job number" value="" />
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
    }
