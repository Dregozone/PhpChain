<?php 

    namespace app\Controller;

    Class AppController 
    {

        // Use API to find the Job that a SN belongs to
        public function apiGetJobBySn($sn) {
            
            $job = "JobOf" . $sn;

            return $job;
        }

        // Use the API to find the operation that a SN is expected at next
        public function apiGetOpBySn($sn) {
            
            $op = "OperationOf" . $sn;

            return $op;
        }

        // Use API to get list of routings
        public function apiGetRoutings() {

            $routings = [
                "ROUTING001" => [
                    "Initialisation" => [ "sequence" => 0, "name" => "Initialisation", "details" => "Scan here to initialise a new SN into this routing." ],
                    "Op 1" => [ "sequence" => 1, "name" => "Op 1", "details" => "Op 1 info." ],
                    "Op 2" => [ "sequence" => 2, "name" => "Op 2", "details" => "Op 2 info." ],
                    "Op 3" => [ "sequence" => 3, "name" => "Op 3", "details" => "Op 3 info." ],
                ]
            ];

            return $routings;
        }

        // Use API to get list of defects
        public function apiGetDefects() {

            $defects = [
                "DefectsSN001" => [[ "defectID" => 1, "defectName" => "Missing part", "status" => "Defective", "version" => 1 ], [ "defectID" => 3, "defectName" => "Incorrect part", "status" => "Defective", "version" => 1 ]], /* 2 defects against this SN */
                "DefectsSN002" => [[ "defectID" => 2, "defectName" => "Damaged part", "status" => "Defective", "version" => 1 ]] /* only 1 defect against this SN */
            ];

            return $defects;
        }

        // Use API to add transaction against SN
        public function apiAddTransaction($sn, $job, $operation, $user) {

            $now = (new \DateTime())->format("Y-m-d H:i:s");

            //

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
