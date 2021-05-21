<?php 

    namespace app\Model;

    Class History extends AppModel 
    {
        private $snTransactions = [];
        private $snDefects = [];
        private $routing = [];

        public function __construct() {
            //
        }
        
        public function setSnTransactions( $snTransactions ) {
            $this->snTransactions = $snTransactions;
        }

        public function setSnDefects( $snDefects ) {
            $this->snDefects = $snDefects;
        }

        public function setRouting( $routing ) {
            $this->routing = $routing;
        }

        public function getSnTransactions() {

            return $this->snTransactions;
        }

        public function getSnDefects() {

            return $this->snDefects;
        }

        public function getRouting() {

            return $this->routing;
        }
    }
