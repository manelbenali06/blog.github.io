<?php

$dbhost= 'localhost';
$dbname= 'formation_dwwm13_php_blog';
$dbuser= 'root';
$dbpass= '';

try {
    $db = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass);
} catch (PDOException $e) {
    // echo $e->getMessage();
    echo 'Une erreur est survenue';
    die($e);
}

?>