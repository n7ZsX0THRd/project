<?php

//Making a connection with the database
function pdo_connect() {
    $hostname = "mssql2.iproject.icasites.nl,1433"; //(local) for your local db
    $dbname = "iproject2"; //database name
    $username = "iproject2"; //your username
    $pw = "PHd1LgMs"; //your password

    global $db;
    $db = new PDO ("sqlsrv:Server=$hostname;Database=$dbname;ConnectionPooling=0","$username","$pw");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

function delete_user($gebruikersnaam) {
    try {
        $db = $pdo->prepare(" DELETE FROM Gebruikers WHERE gebruikersnaam = ? ");
        $db->execute(array($gebruikersnaam));
    } catch (PDOException $e) {
        echo "Could not delete user, ".$e->getMessage();
    }
}
?>
