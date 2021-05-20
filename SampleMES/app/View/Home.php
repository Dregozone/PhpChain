<?php 
    
    namespace app\View;

    Class Home 
    {
        private $model;
        private $controller;
        
        public function __construct($model, $controller) {
            $this->model = $model;
            $this->controller = $controller;
        }
        
        public function loggedInAs($user) {

            $html = "
                <div class=\"loggedInAs\">
                    Logged in as: $user.
                </div>
            ";

            return $html;
        }

        public function title($title) {

            $html = '
                <h1>
                    ' . $title . '
                </h1>
            ';

            return $html;
        }

        public function startContainer($user) {

            $html = '
                <input type=text" id="user" style="display: none;" value="' . $user . '" />
            
                <div style="display: flex; flex-wrap: wrap;">
            ';

            return $html;
        }

        public function endContainer() {

            $html = '
                </div>
            ';

            return $html;
        }

        public function snSearch() {

            $html = '
                <div style="width: 48%; margin: 1%;">
                    <h2>View SN</h2>
                    
                    <label for="sn">Serial number: </label>
                    <input type="text" class="form-control homeInput" name="sn" id="sn" placeholder="SN" />
                    
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="searchSn()">
                            Search
                        </div>
                    </div>
                </div>
            ';

            return $html;
        }

        public function snResults() {

            $html = '
                <div style="width: 48%; margin: 1%;">
                    <h2>SN Results</h2>
                    
                    <div id="snResults"></div>
                </div>
            ';

            return $html;
        }

        public function addTransaction() {

            $html = '
                <div style="width: 48%; margin: 1%;">
                    <h2>Add Transaction</h2>
                    
                    <label for="snAdd">SN: </label>
                    <input type="text" class="form-control homeInput" name="snAdd" id="snAdd" placeholder="Serial Number" />
                    
                    <br />
                    
                    <label for="transactionAdd">Action: </label>
                    <input type="text" class="form-control homeInput" name="" id="transactionAdd" placeholder="Transaction" />
                    
                    <br />
                    
                    <div style="text-align: center;">
                        <div class="btn btn-info moveLeft" onclick="addTransaction()">
                            Add transaction
                        </div>
                    </div>
                </div>
            ';

            return $html;
        }

        public function addTransactionResults() {

            $html = '
                <div style="width: 48%; margin: 1%;">
                    <h2>Creating new transaction</h2>
                    
                    <div id="addTransaction"></div>
                </div>
            ';

            return $html;
        }
    }
