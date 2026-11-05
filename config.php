<?php

$serverName = "localhost";
$userName = "root";
$password = "root";
$databaseName = "actor_agency";

try {
    $connection = new PDO(
        "mysql:host=$serverName;dbname=$databaseName",
        $userName,
        $password
    );

    $connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}
