<?php 

    Class Transaction 
    {
        public $data = [];

        private $who;
        private $what;
        private $when;
        private $why;
        private $where;
        private $how;

        public function __construct($who, $what, $when, $why, $where, $how) {
            $this->who = $who;
            $this->what = $what;
            $this->when = $when;
            $this->why = $why;
            $this->where = $where;
            $this->how = $how;

            $this->data = [
                "Who" => $this->who,
                "What" => $this->what,
                "When" => $this->when,
                "Why" => $this->why,
                "Where" => $this->where,
                "How" => $this->how
            ];
        }

        public function getData() {

            return $this->data;
        }
    }
