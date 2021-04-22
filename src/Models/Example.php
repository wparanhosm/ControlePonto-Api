<?php

    class Example{

        private $id_example;
        private $ds_example;

        public function __construct(){

        }

        public function getId(){
            return $this->id_example;
        }

        public function setId($id){
            $this->id_example = $id;
            return $this;
        }

        public function getDs(){
            return $this->ds_example;
        }

        public function setDs($ds){
            $this->ds_example = $ds;
            return $this;
        }

        public function getJSONEncode() {
            return get_object_vars($this);
        }
    }

?>