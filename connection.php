<?php

    $database= new mysqli("localhost","root","admin1234567890","edoc");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>