<?php 

    namespace app\Controller;

    Class AppController 
    {
        // Use API to check whether this combination of username and PKI are legit: If true, login, else block login and log user out (handled in Login)
        public function apiCheckCredentials( $username ) {
            
            //

            return true;////debug
        }

        // Use API to find the Job that a SN belongs to
        public function apiGetJobBySn($sn) {

            // Prepare values for API
            $action = "getJobBySn";
            $user = $this->model->getUser();

            // Run API
            $jobFromApi = include '../Communication/API.php';

            return $jobFromApi;
        }

        // Use the API to find the operation that a SN is expected at next
        public function apiGetOpBySn($sn) {

            // FIND JOB BY SN
            // Prepare values for API
            $action = "getJobBySn";
            $user = $this->model->getUser();
            // Run API
            $jobFromApi = include '../Communication/API.php';

            // FIND ROUTING INFO
            // Prepare values for API
            $action = "getRoutings";
            $user = $this->model->getUser();
            // Run API
            $routingsFromApi = include '../Communication/API.php';
            $routing = $routingsFromApi[$jobFromApi];

            // FIND SN COMPLETED TRANSACTIONS
            // Prepare values for API
            $action = "getTransactions";
            $user = $this->model->getUser();
            // Run API
            $transactionsFromApi = include '../Communication/API.php';
            $snTransactions = $transactionsFromApi[$sn];

            /*
            echo "<pre>";
            var_dump( $snTransactions );
            echo "</pre>";
            //die();
            */

            // Build array of operation names that have been completed  
            $snTransactionOperations = [];
            foreach ( $snTransactions as $details ) {
                $snTransactionOperations[] = $details["operation"];
            }

            // Loop through all operations in this routing to find the first without a valid transaction recorded
            foreach ( $routing as $operation => $details ) {

                /*
                var_dump( $operation );
                echo "<hr />";
                var_dump( array_reverse($snTransactionOperations, true) );
                echo "<hr />";
                */

                if (
                    !in_array($operation, $snTransactionOperations) || /* This has never been done */
                    ( 
                        in_array("UNDO-" . $operation, $snTransactionOperations) && /* This operation has been undone, and ... */
                        ( array_search("UNDO-" . $operation, array_reverse($snTransactionOperations, true)) > array_search($operation, array_reverse($snTransactionOperations, true)) ) /* ... it has not been re-completed since. Uses indexof check to see the latest addition to array. Use array_reverse to start from the most recent transactions working backwards */
                    )
                ) {
                    // This is the first incomplete operation, return this value... 

                    /*
                    echo "Searching for \"UNDO-{$operation}\" in array: ";
                    var_dump( array_reverse($snTransactionOperations, true) );
                    */

                    //echo array_search("UNDO-" . $operation, array_reverse($snTransactionOperations, true)) . " > " . array_search($operation, array_reverse($snTransactionOperations, true));
                    //var_dump( array_search("UNDO-" . $operation, array_reverse($snTransactionOperations, true)) > array_search($operation, array_reverse($snTransactionOperations, true)) );

                    //var_dump( $operation );
                    //die();

                    return $operation;
                }
            }

            return false;
        }

        // Use API to get list of routings
        public function apiGetRoutings() {

            // Prepare values for API
            $action = "getRoutings";
            $user = $this->model->getUser();

            // Run API
            $routingsFromApi = include '../Communication/API.php';

            return $routingsFromApi;
        }

        // Use API to get list of defects
        public function apiGetDefects() {

            // This will use blockchains
            $defects = [
                "DefectsSN001" => [
                    [ "defectID" => 1, "defectName" => "Missing part", "status" => "Defective", "version" => 1 ],
                    [ "defectID" => 3, "defectName" => "Incorrect part", "status" => "Defective", "version" => 1 ]
                ],
                "DefectsSN002" => [
                    [ "defectID" => 2, "defectName" => "Damaged part", "status" => "Defective", "version" => 1 ]
                ]
            ];

            return $defects;
        }

        // Use API to get list of transactions
        public function apiGetTransactions() {

            // Prepare values for API
            $action = "getTransactions";
            $user = $this->model->getUser();

            // Run API
            $transactionsFromApi = include '../Communication/API.php';

            return $transactionsFromApi;
        }

        // Use API to add transaction against SN
        public function apiAddTransaction($sn, $job, $operation, $user) {

            // Prepare values for API
            $action = "addTransaction";
            $now = (new \DateTime())->format("Y-m-d H:i:s");

            // Run API
            $fromApi = include '../Communication/API.php';
        }

        // Use API to add defect against SN
        public function apiAddDefect($sn, $defectName, $user) {

            $now = (new \DateTime())->format("Y-m-d H:i:s");

            //

        }

        // Use API to update defect status
        public function apiUpdateDefect( $sn, $defectId, $status, $user) {
            
            $now = (new \DateTime())->format("Y-m-d H:i:s");

            //

        }

        // Use API to update all routings
        public function apiUpdateRoutings( $updatedRoutings, $user ) {
            //
        }
    }
