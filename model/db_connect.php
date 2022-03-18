<?php

$hostname = "localhost";
$user = "root";
$pass = "admin";
$db = "projeto_crud";

$connect = mysqli_connect($hostname,$user,$pass,$db);

if( mysqli_connect_error() ):
    echo "Erro de conexão: ".mysqli_connect_error();
endif;

?>