<?php

$dbname= "servigera";

if(!($id = mysqli_connect("localhost","root"))){
    echo "Não foi possivel estabelecer uma conexão com o gerenciador MySQL.";
    exit;
    
}if(!($con = mysqli_select_db ($id, $dbname))){
    echo "Não foi possivel estabelecer uma conexão com o banco.";
    exit;
}

mysqli_query($id, "SET time_zone = '-03:00'");


?>