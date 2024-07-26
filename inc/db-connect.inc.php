<?php

try {
    $host = DB_HOST;
    $dbName = DB_DATABASE_NAME;
    $username = DB_USERNAME;
    $password = DB_PASSWORD;
    $dsn = 'mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch (PDOException $e) {
    var_dump($e->getMessage());
    echo 'A problem occured with the database connection...';
    die();
}

return $pdo;