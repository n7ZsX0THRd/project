<?php
session_start();
include ('database.php');
include ('user.php');
pdo_connect();

$email = $_SESSION['email'];
$queryGebruikersnaam="SELECT TOP(1) gebruikersnaam FROM Gebruikers WHERE emailadres = '$email'";
$resultGebruikersnaam = $db->query($queryGebruikersnaam)->fetchall()[0];


$target_dir = "../images/users/";
$uploadOk = 1;
$imageFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
$imageFileName = $resultGebruikersnaam[0] . '.' . $imageFileType;
$queryBestandsnaam="UPDATE Gebruikers SET bestandsnaam = '$imageFileName' WHERE Gebruikersnaam = '$resultGebruikersnaam[0]'";
$wijzigenBestandsnaam = $db->query($queryBestandsnaam);
$target_file = $target_dir . $imageFileName;

/// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Bestand is geen afbeelding!";
        $uploadOk = 0;
    }
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 1000000) {
    echo "Sorry, bestand is te groot!";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
    echo "Alleen jpg, jpeg, png en gif!";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, is is iets misgegaan, probeer het opnieuw!";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $wijzigenBestandsnaam;
        echo "Je profielfoto is geüpload!";
    } else {
        echo "Sorry, is is iets misgegaan, probeer het opnieuw!";
    }
}
?>


