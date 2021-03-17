<?php
    require_once 'connection.php';

    $sql = "SELECT * FROM TB_EXAMPLE";

    $statement = $conn->prepare($sql);
    $statement->execute();
    
    $res = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    $json = json_encode($res,JSON_UNESCAPED_UNICODE);

    echo $json;
?>