<?php

    require_once 'connection.php';

    $sql = "SELECT * FROM `tb_example`";

    $statement = $conn->prepare($sql);
    $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

    // echo phpinfo();
?>