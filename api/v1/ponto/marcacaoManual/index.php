<?php
namespace Api\Ponto;

require realpath('../../../../vendor/autoload.php');
include( '../../../../src/Helpers/headers.php' );
include( '../../../../src/Helpers/generic.php' );

try {
    if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
        try {
            $headers = apache_request_headers();
            $data = json_decode( file_get_contents( 'php://input' ) );
            $args = json_decode( file_get_contents( 'php://input' ), TRUE );

            if ( $data === null && json_last_error() !== JSON_ERROR_NONE ) {
                echo json_encode( [ ['status' => '403', 'info' => 'Payload Precondition Failed'] ] );
                die();
            }
            # Verify Header Authorization Field
            if ( !isset( $headers['Authorization'] ) ) {
                echo json_encode( [ ['status' => '402', 'info' => 'Invalid or Missing Token'] ] );

                die();
            }

            if ( sizeof( $args ) != 2 ) {
                echo json_encode( [ ['status' => '102', 'info' => 'Invalid Arguments Number (Expected Four)'] ] );
                die();
            }
            
        } catch ( Exception $ex ) {
            echo json_encode( [ ['status:' => '400', 'info' => 'Bad Request (Invalid Syntax)'] ] );
            die();
        }

        $err = array();
        try {
            ( !isset( $data->dt_marcacao ) ? array_push( $err, 1 ):NULL );
            ( !isset( $data->tipo_marcacao ) ? array_push( $err, 1 ):NULL );

            if ( sizeof( $err ) > 0 ) {
                echo json_encode( [ ['status' => '403', 'info' => 'Payload Precondition Failed'] ] );
                die();
            }
        } catch ( \Exception $ex ) {
            echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage()] ] );
            die();
        }   



        # Loads: User and UserModel
        $ponto = new \Api\Ponto\Ponto();
        $pontoModel = new \Api\Ponto\PontoModel();


        $auth = $headers['Authorization'];

        $user = new \Api\User\User();
        $userModel = new \Api\User\UserModel();

        try{
            $user = $userModel->searchByToken($auth);
        } catch (\Exception $ex){
            echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage() ] ] );
        }



        if($user == false){
            echo json_encode( [ ['status' => '406', 'info' => "Usuário não cadstrado" ] ] );
            die();
        }

        $ponto->setId_usuario($user->id);
        $ponto->setDt_marcacao($data->dt_marcacao);
        $ponto->setTipo_marcacao($data->tipo_marcacao);
        $ponto->setMarcacao_manual(1);

        try {
            $response = $pontoModel->marcacaoManual($ponto);
            echo json_encode( $response == 1 ? true : $response );

        } catch( \PDOException $e ) {
            echo json_encode( [ ['status' => '405', 'info' => SQLMessage( $e->getCode() ) ] ] );
        }

    } else {
        echo json_encode( [ ['status' => '404', 'info' => 'Method Not Allowed' ] ] );
    }
} catch( \Exception $ex ) {
    echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage() ] ] );
    die();
}
