<?php 

    namespace app\Model;

    Class Work extends AppModel 
    {
        // Current routing
        private $routing = [];

        // Operation details
        private $sequence;
        private $name;
        private $details;

        // Defects
        private $defects = [];

        public function __construct() {
            //
        }

        public function setRouting( array $routing ) {
            $this->routing = $routing;
        }

        public function setSequence( $sequence ) {
            $this->sequence = $sequence;
        }
        
        public function setName( $name ) {
            $this->name = $name;
        }

        public function setDetails( $details ) {
            $this->details = $details;
        }

        public function setDefects( array $defects ) {
            $this->defects = $defects;
        }

        public function getRouting() {

            return $this->routing;
        }

        public function getSequence() {

            return $this->sequence;
        }

        public function getName() {

            return $this->name;
        }

        public function getDetails() {

            return $this->details;
        }

        public function getDefects() {

            return $this->defects;
        }
    }
