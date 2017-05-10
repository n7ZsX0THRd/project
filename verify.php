<?php
session_start();

include ('php/dataabase.php');
pdo_connect();

if(isset($_GET['gebruikersnaam']) && !empty($_GET['gebruikersnaam']) && isset($_GET['code']) && !empty($_GET['code'])){
    // Verify data
    $email = $_GET['gebruikersnaam'];
    $code = $_GET['code'];
}else{
    // Invalid approach
        echo 'Invalid approach, please use the link that has been send to your email.';

}
?>