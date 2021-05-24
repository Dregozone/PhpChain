<?php 

    namespace app\Controller;

    Class AppController 
    {
        // Use API to check whether this combination of username and PKI are legit: If true, login, else block login and log user out (handled in Login)
        public function apiCheckCredentials( $username ) {
            
            // Prepare values for API
            $action = "checkCredentials";

            // Run API
            $isValid = include '../Communication/API.php';

            return $isValid;
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
            $mostRecentRouting = sizeof( $routingsFromApi[$jobFromApi] ) - 1;
            $routing = $routingsFromApi[$jobFromApi][$mostRecentRouting];

            // FIND SN COMPLETED TRANSACTIONS
            // Prepare values for API
            $action = "getTransactions";
            $user = $this->model->getUser();
            // Run API
            $transactionsFromApi = include '../Communication/API.php';
            $snTransactions = $transactionsFromApi[$sn];

            // Build array of operation names that have been completed  
            $snTransactionOperations = [];
            foreach ( $snTransactions as $details ) {
                $snTransactionOperations[] = $details["operation"];
            }

            // Loop through all operations in this routing to find the first without a valid transaction recorded
            foreach ( $routing as $operation => $details ) {

                if (
                    !in_array($operation, $snTransactionOperations) || /* This has never been done */
                    ( 
                        in_array("UNDO-" . $operation, $snTransactionOperations) && /* This operation has been undone, and ... */
                        ( array_search("UNDO-" . $operation, array_reverse($snTransactionOperations, true)) > array_search($operation, array_reverse($snTransactionOperations, true)) ) /* ... it has not been re-completed since. Uses indexof check to see the latest addition to array. Use array_reverse to start from the most recent transactions working backwards */
                    )
                ) {
                    // This is the first incomplete operation, return this value... 

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

            // Prepare values for API
            $action = "getDefects";
            $user = $this->model->getUser();

            // Run API
            $defectsFromApi = include '../Communication/API.php';

            return $defectsFromApi;
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

            // Prepare values for API
            $action = "addDefect";
            $now = (new \DateTime())->format("Y-m-d H:i:s");

            // Run API
            $fromApi = include '../Communication/API.php';
        }

        // Use API to update defect status
        public function apiUpdateDefect( $sn, $defectId, $status, $user) {
            
            // Prepare values for API
            $action = "updateDefect";
            $now = (new \DateTime())->format("Y-m-d H:i:s");

            // Run API
            $fromApi = include '../Communication/API.php';
        }

        // Use API to update routing
        public function apiUpdateRouting( $updatedRouting, $routingName, $user ) {

            // Prepare values for API
            $action = "updateRouting";
            $now = (new \DateTime())->format("Y-m-d H:i:s");

            // Run API
            $fromApi = include '../Communication/API.php';
        }
    }
