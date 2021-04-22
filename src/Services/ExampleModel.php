<?php


   

    $root =  $_SERVER['DOCUMENT_ROOT'];
    
    include("$root/src/Models/Example.php");
    include("$root/src/Database/Database.php");

    class ExampleModel{
        private $pdo;
        
        public function __construct(){
            $this->pdo = Database::connection();
        }

        public function GetAll(){

            $consulta = $this->pdo->query("SELECT `id_example`,`ds_example` FROM `tb_example`");

            $arr = [];

            
            while ($linha = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $obj = new Example();

                $obj->setId($linha['id_example']);
                $obj->setDs($linha['ds_example']);

                $arr[] = $obj->getJSONEncode();
            }



            return $arr;
        }
        




    }



?>