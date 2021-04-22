 <?php
    $root =  $_SERVER['DOCUMENT_ROOT'];
    include("$root/src/Services/ExampleModel.php");
    
    $userModel = new ExampleModel();

    // $results = $model->getAll();

    // echo json_encode($results);
    try {
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {

            try {
                $res = [];
                $res['status'] = "201"; 
                $res['data'] = $userModel->getAll();
                
                echo json_encode($res);
                die();
            }
            catch( PDOException $e ) {
                echo json_encode( [ ['status' => '405', 'info' => SQLMessage( $e->getCode() ) ] ] );
            }

        } else {
            echo json_encode( [ ['status' => '404', 'info' => 'Method Not Allowed' ] ] );
        }

    } catch( \Exception $ex ) {
        echo json_encode( [ ['status' => '406', 'info' => $ex->getMessage() ] ] );
        die();
    }

?> 