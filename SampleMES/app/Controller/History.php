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
            $job = $this->apiGetJobBySn($sn);
            $routings = $this->apiGetRoutings();

            $this->model->setSnTransactions( $transactions[$sn] );
            $this->model->setSnDefects( $defects["Defects" . $sn] );
            $this->model->setRouting( $routings[$job] );

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
