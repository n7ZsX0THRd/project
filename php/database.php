<?php
function pdo_connect() {
    $hostname = "mssql2.iproject.icasites.nl"; 
    $dbname = "iproject2";
    $username = "iproject2";
    $pw = " PHd1LgMs";

    global $db;
    $db = new PDO ("sqlsrv:Server=$hostname;Database=$dbname;ConnectionPooling=0","$username","$pw");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
?>