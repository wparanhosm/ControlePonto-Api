<?php

namespace Api\User;

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

            // if ( !isset( $headers['Authorization'] ) ) {
            //     echo json_encode( [ ['status' => '402', 'info' => 'Invalid or Missing Token'] ] );

            //     die();
            // }

            if ( sizeof( $args ) != 4 ) {
                echo json_encode( [ ['status' => '102', 'info' => 'Invalid Arguments Number (Expected Four)'] ] );
                die();
            }

        } catch ( \Exception $ex ) {
            echo json_encode( [ ['status:' => '400', 'info' => 'Bad Request (Invalid Syntax)'] ] );
        }

        # Load classes
        $user = new \Api\User\User();
        $userModel = new \Api\User\UserModel();

        # Access TOKEN verification
        // try {
        //     if ( !$userModel->auth( $headers['Authorization'] ) ) {
        //         echo json_encode( [ ['status' => '401', 'info' => 'Token Refused'] ] );
        //         die();
        //     }

        // } catch ( \Exception $ex ) {
        //     echo json_encode( [ ['status' => '407', 'info' => $ex->getMessage()] ] );
        //     die();
        // }

        # verification payload fields
        $err = [];
        try {
            ( !isset( $data->name ) ? array_push( $err, 1 ):NULL );
            ( !isset( $data->email ) ? array_push( $err, 1 ):NULL );
            ( !isset( $data->username ) ? array_push( $err, 1 ):NULL );
            ( !isset( $data->password ) ? array_push( $err, 1 ):NULL );

            if ( sizeof( $err ) > 0 ) {
                echo json_encode( [ ['status' => '403', 'info' => 'Payload Precondition Failed'] ] );
                die();
            }

        } catch ( \Exception $ex ) {
            echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage()] ] );
            die();
        }

        try {
            $user->setUsername( strip_tags( $data->username ) );
            $user->setPassword( strip_tags( $data->password ) );

            if ( $userModel->checkUser( $user ) ) {
                echo json_encode( [ ['status' => '104', 'info' => 'User Already Exists'] ] );
                die();
            }

            # Create new User
            $insert = $userModel->insert(
                $user->setName( strip_tags( $data->name ) ),
                $user->setEmail( strip_tags( $data->email ) ),
                $user->setUsername( strip_tags( $data->username ) ),
                $user->setPassword( strip_tags( $data->password ) )
            );

            if ( $insert ) {
                echo json_encode( [ ['status' => '201', 'info' => 'User Successfully Added'] ] );
            } else {
                echo json_encode( [ ['status' => '204', 'info' => 'Could Not Add User'] ] );
            }
            die();

        } catch ( \PDOException $e ) {
            echo json_encode( [ ['status' => '405', 'info' => SQLMessage( $e->getCode() ) ] ] );
            die();
        }
    } else {
        echo json_encode( [ ['status' => '404', 'info' => 'Method Not Allowed' ] ] );
        die();
    }
} catch( \Exception $ex ) {
    echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage() ] ] );
    die();
}
