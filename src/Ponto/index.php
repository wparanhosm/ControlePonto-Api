<?php
header( 'Content-Type: application/json; charset=UTF-8' );
http_response_code( 405 );
echo json_encode( [ ['status' => '-1', 'info' => 'Method Not Allowed' ] ] );
die();