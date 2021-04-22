<?php

    $user    = 'id16439865_wmiranda';
    $pass    = 'Uni9@20218sem';
    $host    = 'localhost';
    $db      = 'id16439865_projetopontodb';
    $charset = 'utf8mb4';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    } 

?>