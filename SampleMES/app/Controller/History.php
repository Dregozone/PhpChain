<?php 
    
    namespace app\Controller;

    Class History extends AppController
    {
        protected $model;
        
        public function __construct($model) {
            $this->model = $model;
        }
        
        public function viewSn() {
            
            $sn = $this->model->getVar("sn");
            $transactions = $this->apiGetTransactions();
            $defects = $this->apiGetDefects();
            $job = $this->apiGetJobBySn($sn) !== false ? $this->apiGetJobBySn($sn) : '';
            $routings = $this->apiGetRoutings();

            if ( array_key_exists($sn, $transactions) ) {
                $this->model->setSnTransactions( $transactions[$sn] );
            } else {
                $this->model->setSnTransactions( [] );
            }

            if ( array_key_exists($sn, $defects) ) {
                $this->model->setSnDefects( $defects[$sn] );
            } else {
                $this->model->setSnDefects( [] );
            }

            if ( array_key_exists($job, $routings) ) {
                $this->model->setRouting( $routings[$job] );
            } else {
                $this->model->setRouting( [] );
            }

            if ( isset($_GET["sn"]) && isset($_GET["msg"]) ) { // An action has just been processed against this SN

                $sn = $this->model->getVar("sn");
                $msg = $this->model->getVar("msg");
                $operation = $this->model->getVar("operation");

                if ( $msg == "UndoTransaction" ) {    
                    $this->model->addNotice("Successfully removed \"" . $operation . "\" transaction against SN: " . $sn . ".");
                }
            }
        }

        public function undoTransaction() {

            $sn = $this->model->getVar("sn");
            $operation = $this->model->getVar("transaction");
            $job = $this->apiGetJobBySn($sn);

            // Use API in AppController to undo the transaction
            $this->apiAddTransaction($sn, $job, "UNDO-" . $operation, $this->model->getUser());

            // Then redirect to clean URL for viewing the updated history of SN
            header("location: ?p=History&action=viewSn&sn=" . $sn . "&msg=UndoTransaction&operation=" . $operation);
        }

        /* //// This is covered already by updateDefect in Work
        public function undoDefectChange() {
            
            // Use API in AppController to undo the defect status change
            ////

            // Then redirect to clean URL at action=viewSn, sn=$sn
            // header("location: ... ");
        }
        */
    }
