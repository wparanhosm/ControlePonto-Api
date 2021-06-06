<?php
namespace Api\Ponto;

require realpath('../../../../vendor/autoload.php');
include( '../../../../src/Helpers/headers.php' );
include( '../../../../src/Helpers/generic.php' );

try {
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        try {
            $headers = apache_request_headers();
           
            # Verify Header Authorization Field
            if ( !isset( $headers['Authorization'] ) ) {
                echo json_encode( [ ['status' => '402', 'info' => 'Invalid or Missing Token'] ] );

                die();
            }
            
        } catch ( Exception $ex ) {
            echo json_encode( [ ['status:' => '400', 'info' => 'Bad Request (Invalid Syntax)'] ] );
            die();
        }

        # Loads: User and UserModel
        $ponto = new \Api\Ponto\Ponto();
        $pontoModel = new \Api\Ponto\PontoModel();


        $auth = $headers['Authorization'];

        try {
            $search = $pontoModel->marcacaoAutomatica( $auth );
            echo json_encode( $search == 1 ? true : $search );
        } catch( \PDOException $e ) {
            echo print_r($e);
            //echo json_encode( [ ['status' => '405', 'info' => SQLMessage( $e->getCode() ) ] ] );
        }

    } else {
        echo json_encode( [ ['status' => '404', 'info' => 'Method Not Allowed' ] ] );
    }
} catch( \Exception $ex ) {
    echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage() ] ] );
    die();
}
