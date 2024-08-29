<?php
    $hostname = "localhost";
    $user = "root";
    $senha = "";
    $dbname = "banco";

    $conn = new mysqli($hostname, $user, $senha, $dbname);

    if (!$conn){
        die ("Não conectado");
    }else{
        echo"conectou <br>";
    }
?>