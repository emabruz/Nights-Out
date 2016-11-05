<?php

    $host = getenv('IP');
    $user = getenv('C9_USER');
    $password = "";
    $db = "nightsoutdb";
    $dbport = 3306;

    $connessione = new mysqli($host, $user, $password, $db, $dbport);

    if ($connessione->connect_error) {
        die("Connection failed: " . $connessione->connect_error);
    } 
    
?>