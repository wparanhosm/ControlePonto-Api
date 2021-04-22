<?php
header( 'Content-Type: application/json; charset=UTF-8' );
echo json_encode( [ ['status' => '404', 'info' => 'Method Not Allowed' ] ] );
die();