<?php 

    Class Handler 
    {
        private $user;
        private $file;
        private $sns = [];

        public function __construct($user) {

            $this->user = $user;
            $this->file = '../data/' . $user . '.json';
        }

        public function saveToFile() {
            file_put_contents($this->file, json_encode($this->sns));
        }

        public function loadFromFile() {

            if ( file_exists($this->file) ) {
                $this->sns = json_decode(file_get_contents($this->file), true);
            } else {
                $this->sns = [];
            }
        }

        public function addTransaction($sn, $action) {

            // This is the first time the SN is transacted against
            if ( !array_key_exists($sn, $this->sns) ) {
                $this->sns[$sn] = serialize( new Blockchain( $action ) );
            } else {
                $this->sns[$sn] = unserialize($this->sns[$sn]);
                $this->sns[$sn]->addBlock( new Block( $action ) );
                $this->sns[$sn] = serialize($this->sns[$sn]);
            }

            return true;
        }

        public function setSns($sns) {
            $this->sns = $sns;
        }

        public function getSns() {

            return $this->sns;
        }

        public function getSn($sn) {
            
            return unserialize( $this->sns[$sn] );
        }
    }
