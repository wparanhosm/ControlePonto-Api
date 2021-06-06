<?php

namespace Api\Ponto;


use \YaLinqo\Enumerable;
use \Api\User;

class PontoModel
{

    private static $pdo;

    /**
    * Model Constructor
    */

    public function __construct() {
        self::$pdo = \Api\Database\Database::connection();
    }




    function getPontos($token) {

        $str = explode(" ",$token);
        if(strtoupper($str[0]) == "BEARER"){
            $token = $str[1];
        }

        try {
            $sql = "select p.* from ponto p inner join users on users.id = p.id_usuario 
            where users.authToken =:token";

            $stmt = self::$pdo->prepare( $sql );
            $stmt->bindValue(':token',$token);// \PDO::PARAM_STR );
            $stmt->execute();

            $lista = array();
            while($item = $stmt->fetch(\PDO::FETCH_OBJ)){
                
                $item->marcacao_manual = $item->marcacao_manual == 1 ? true : false;
               
                $lista[] = $item;
            }

            return  Enumerable::from($lista)->toArrayDeep();


            // return $lista;
        } catch ( \PDOException $ex ) {
            throw $ex;
        }
    }



    function marcacaoAutomatica($token){
        
        date_default_timezone_set('America/Sao_Paulo');

        $str = explode(" ",$token);
        if(strtoupper($str[0]) == "BEARER"){
            $token = $str[1];
        }
        
        
        $marcacaoAutomatica = 1;

        $dia = date("Y-m-d");
        
        //validando se já existe uma marcação para esse dia 

        $sql = "select p.* from ponto p inner join users on users.id = p.id_usuario 
                where users.authToken = :token
                and p.dt_marcacao between :ini and :fin";

        $stmt = self::$pdo->prepare( $sql );
        $stmt->bindValue(':token',$token);// \PDO::PARAM_STR );
        $stmt->bindValue(':ini',$dia.' 00:00:00');// \PDO::PARAM_STR );
        $stmt->bindValue(':fin',$dia.' 23:59:59');// \PDO::PARAM_STR );
        $stmt->execute();

        $listaMarcacoes = array();
        while($item = $stmt->fetch(\PDO::FETCH_OBJ)){
            
            $item->marcacao_manual = $item->marcacao_manual == 1 ? true : false;
            
            $listaMarcacoes[] = $item;
        }

        

        $registrosMarcacoes = Enumerable::from($listaMarcacoes)->count();

        //verificando se ja há 2 registros no banco 

        if($registrosMarcacoes >= 2){
            return ["400" => "Já existe marcação de entrada e saída no dia atual"];
        }



        //verificando se há alguma marcação ímpar no banco 

        $sql = "select count(p.tipo_marcacao) as marcacoes, max(p.dt_marcacao) as dt_marcacao from ponto p
        inner join users
        on users.id = p.id_usuario 
        where users.authToken = :token and p.dt_marcacao not between :ini and :fin
        group by DAY(p.dt_marcacao)
        having count(p.tipo_marcacao) = 1";



        $stmt = self::$pdo->prepare( $sql );
        $stmt->bindValue(':token',$token);
        $stmt->bindValue(':ini',$dia.' 00:00:00');// \PDO::PARAM_STR );
        $stmt->bindValue(':fin',$dia.' 23:59:59');// \PDO::PARAM_STR );
        $stmt->execute();

        $lista = array();
        while($item = $stmt->fetch(\PDO::FETCH_OBJ)){            
            $lista[] = $item;
        }

        $registros = Enumerable::from($lista)->count();

        if($registros > 0){
            $errors = array();
            foreach ($lista as $key => $value) {
                $date = date('d/m/Y', strtotime($value->dt_marcacao));
                $errors[] = ["401" => "Marcação ímpar: falta marcação para o dia $date"];
            }

            return $errors;
        }


        //se não houver registros no banco, e nem marcação ímpar, basta verificar qual será o tipo de marcação a ser efetuado no dia 

        $numeroRegistrosEntrada = Enumerable::from($listaMarcacoes)
        ->where('$prod ==> $prod->tipo_marcacao == 1')
        ->count();

        $tipo_marcacao = 0;

        if($numeroRegistrosEntrada == 0){
            $tipo_marcacao = 1;
        } else {
            $tipo_marcacao = 2;
        }


        //buscando id do usuário 

        $id_user = 0;


        $sql = "select id from users where authToken = :token";

        $stmt = self::$pdo->prepare( $sql );
        $stmt->bindValue(':token',$token);
        $stmt->execute();

        if ( $stmt->rowCount() == 1 ) {
             $item = $stmt->fetch( \PDO::FETCH_OBJ );
             $id_user = $item->id;
        } else {
            return ["401" => "Usuário não encontrado no banco de dados"];
        }


        $ponto = new Ponto();

        $ponto->setId_usuario($id_user);
        $ponto->setDt_marcacao(date('Y:m:d H:m:s'));
        $ponto->setTipo_marcacao($tipo_marcacao);
        $ponto->setMarcacao_manual(0);
        return $this->insert($ponto);
    }



    function marcacaoManual(Ponto $ponto){
        date_default_timezone_set('America/Sao_Paulo');
        
        $dia = date("Y-m-d", strtotime($ponto->getDt_marcacao()));
        
        //validando se já existe uma marcação para esse dia 
        $sql = "select p.* from ponto p inner join users on users.id = p.id_usuario 
                where users.id = :id_user
                and p.dt_marcacao between :ini and :fin";

        $stmt = self::$pdo->prepare( $sql );
        $stmt->bindValue(':id_user',$ponto->getId_usuario());// \PDO::PARAM_STR );
        $stmt->bindValue(':ini',$dia.' 00:00:00');// \PDO::PARAM_STR );
        $stmt->bindValue(':fin',$dia.' 23:59:59');// \PDO::PARAM_STR );
        $stmt->execute();

        $listaMarcacoes = array();
        while($item = $stmt->fetch(\PDO::FETCH_OBJ)){
            
            $item->marcacao_manual = $item->marcacao_manual == 1 ? true : false;
            
            $listaMarcacoes[] = $item;
        }

        $registrosMarcacoes = Enumerable::from($listaMarcacoes)->count();

        // se já houver registro de ponto com esse tipo, ele vai sobrescrever a entrada
        if($registrosMarcacoes >= 1){


            //verificando se é a entrada
            if($ponto->getTipo_marcacao() == 1){


                $entrada = Enumerable::from($listaMarcacoes)
                ->where('$prod ==> $prod->tipo_marcacao == 1')
                ->first();

                //verificando se há registro de saída 
                $registrosSaida = Enumerable::from($listaMarcacoes)
                ->where('$prod ==> $prod->tipo_marcacao == 2')
                ->count();

                if($registrosSaida > 0){
                    //verifica se a data de entrada é menor que a data de saída

                    $saida = Enumerable::from($listaMarcacoes)
                    ->where('$prod ==> $prod->tipo_marcacao == 2')
                    ->first();


                    echo strtotime($entrada->dt_marcacao)." ".strtotime($saida->dt_marcacao);

                    if(strtotime($entrada->dt_marcacao) > strtotime($saida->dt_marcacao)){
                        $dt_entrada = date('d/m/Y H:m:s', strtotime($entrada->dt_marcacao));
                        $dt_saida = date('d/m/Y H:m:s', strtotime($saida->dt_marcacao));
                        return ["401" => "Entrada $dt_entrada é posterior a saída $dt_saida"];
                    }
                }
                $ponto->setId_ponto($entrada->id_ponto);
            }


            if($ponto->getTipo_marcacao() == 2 &&   
                $registrosSaida = Enumerable::from($listaMarcacoes)
                ->where('$prod ==> $prod->tipo_marcacao == 2')
                ->count() > 0
            ){
                

                $saida = Enumerable::from($listaMarcacoes)
                    ->where('$prod ==> $prod->tipo_marcacao == 2')
                    ->first();
                
                //verificando se há registro de saída 
                $registrosEntrada = Enumerable::from($listaMarcacoes)
                ->where('$prod ==> $prod->tipo_marcacao == 1')
                ->count();

                if($registrosEntrada > 0){
                    //verifica se a data de entrada é menor que a data de saída

                    $entrada = Enumerable::from($listaMarcacoes)
                        ->where('$prod ==> $prod->tipo_marcacao == 1')
                        ->first();
                    
                    


                    if(strtotime($entrada->dt_marcacao) > strtotime($saida->dt_marcacao)){
                        $dt_entrada = date('d/m/Y H:m:s', strtotime($entrada->dt_marcacao));
                        $dt_saida = date('d/m/Y H:m:s', strtotime($saida->dt_marcacao));
                        return ["401" => "Saída $dt_saida é anterior a entrada $dt_entrada"];
                    }

                    $ponto->setId_ponto($saida->id_ponto);

                } else {
                    return ["401" => "Não é possível adicionar uma saída sem um registro de entrada"];
                }
            }
        
            return $this->update($ponto);

        } 

        return $this->insert($ponto);
    }



    function update( Ponto $ponto ) {
        try {
            self::$pdo->beginTransaction();
            
            $sql = 'UPDATE ponto SET 
                    ponto.id_usuario = :id_user,
                    ponto.dt_marcacao = :dt_marcacao,
                    ponto.tipo_marcacao = :tipo_marcacao,
                    ponto.marcacao_manual = :marcacao_manual
                    WHERE ponto.id_ponto = :id_ponto';

            $stmt = self::$pdo->prepare( $sql );
            $stmt->bindValue( ':id_user',           $ponto->getId_usuario() );
            $stmt->bindValue( ':dt_marcacao',       $ponto->getDt_marcacao());
            $stmt->bindValue( ':tipo_marcacao',     $ponto->getTipo_marcacao());
            $stmt->bindValue( ':marcacao_manual',   $ponto->getMarcacao_manual());
            $stmt->bindValue( ':id_ponto',   $ponto->getId_ponto());

            $stmt->execute();

            self::$pdo->commit();

            return ( string ) TRUE;

        } catch( \PDOException $ex ) {
            self::$pdo->rollback();
            throw $ex;
        }
    }

    function delete (int $id_ponto){
        try {
            self::$pdo->beginTransaction();
            
            $sql = 'DELETE FROM PONTO
                    WHERE ponto.id_ponto = :id_ponto';

            $stmt = self::$pdo->prepare( $sql );
            $stmt->bindValue( ':id_ponto', $id_ponto);

            $stmt->execute();

            self::$pdo->commit();

            return ( string ) TRUE;

        } catch( \PDOException $ex ) {
            self::$pdo->rollback();
            throw $ex;
        }
    }


    function insert(Ponto $ponto){
        try {
            self::$pdo->beginTransaction();

            $sql = 'insert into ponto(id_usuario,dt_marcacao,tipo_marcacao,marcacao_manual)
                    VALUES (:id_user, :dt_marcacao, :tipo_marcacao, :marcacao_manual)';

            $stmt = self::$pdo->prepare( $sql );
            $stmt->bindValue( ':id_user',           $ponto->getId_usuario(), \PDO::PARAM_STR );
            $stmt->bindValue( ':dt_marcacao',       $ponto->getDt_marcacao(), \PDO::PARAM_STR );
            $stmt->bindValue( ':tipo_marcacao',     $ponto->getTipo_marcacao(), \PDO::PARAM_STR );
            $stmt->bindValue( ':marcacao_manual',   $ponto->getMarcacao_manual(), \PDO::PARAM_STR );
            $stmt->execute();

            self::$pdo->commit();

            return ( string ) TRUE;

        } catch( \PDOException $ex ) {
            self::$pdo->rollback();
            throw $ex;
        }
    }
}

?>