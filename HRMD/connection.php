<?php
    $servername = "localhost";
    $email = "root";
    $password = "";
    try {
        $conn
    = new PDO("mysql:host=$servername;dbname=db_login"
    , $email, $password);
    // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
    echo "";
    } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    }
?>