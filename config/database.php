<?php

    $host = getenv('MYSQLHOST');
    $port = getenv('MYSQLPORT');
    $dbname = getenv('MYSQLDATABASE');
    $username = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }

?>
