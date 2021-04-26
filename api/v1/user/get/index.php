<?php
namespace Api\User;

require realpath('../../../../vendor/autoload.php');
include( '../../../../src/Helpers/headers.php' );
include( '../../../../src/Helpers/generic.php' );

try {
    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
        try {
            $headers = apache_request_headers();

            # Verify Header Authorization Field
            if ( !isset( $headers['Authorization'] ) ) {
                echo json_encode( [ ['status' => '402', 'info' => 'Invalid or Missing Token'] ] );

                die();
            }
            
            if (count($_GET) != 1 ) {
                echo json_encode( [ ['status' => '202', 'info' => 'Invalid Arguments Number (Expected Two)'] ] );
                die();
            }

        } catch ( Exception $ex ) {
            echo json_encode( [ ['status:' => '400', 'info' => 'Bad Request (Invalid Syntax)'] ] );
            die();
        }

        # Loads: User and UserModel
        $user = new \Api\User\User();
        $userModel = new \Api\User\UserModel();

        try {
            if ( !$userModel->auth( $headers['Authorization'], $_GET['id'] ) ) {
                echo json_encode( [ ['status' => '401', 'info' => 'Token Refused'] ] );
                die;
            }
        } catch( \Exception $ex ) {
            echo json_encode( [ ['status' => '407', 'info' => $ex->getMessage()] ] );
            die;
        }


        try {

            $userId = $_GET['id'];
            
            if ( $userId > 0 ) {
                $user->setId( $userId );
                $search = array_merge( ['status' => "201"], $userModel->search( $user ) );
                echo json_encode( [$search] );
            } else {
                echo json_encode( [ ['status' => '203', 'info' => 'User Not Found or Incorrect username and/or password'] ] );
            }
            die();

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
