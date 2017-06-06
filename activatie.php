<?php 

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
session_start();

$user= getLoggedInUser($db);
echo var_dump($user);
echo var_dump($_POST);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST["controle"]) && !empty($_POST["controle"])){
        if ($_POST["controle"]=="creditcard"){
            echo "Succes!";
        }
    }
}

?>