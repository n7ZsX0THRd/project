<?php
/*
  iProject Groep 2
  30-05-2017

  file: upload.php
  purpose:
    Upload profilepicture for user
*/
session_start();
include_once ('database.php');
include_once ('user.php');
pdo_connect();
// Start session,
// include database and user functions
// connect to database

$email = $_SESSION['email'];
$queryGebruikersnaam="SELECT TOP(1) gebruikersnaam FROM Gebruikers WHERE emailadres = '$email'";
$resultGebruikersnaam = $db->query($queryGebruikersnaam)->fetchall()[0];


$target_dir = "../images/users/";
$uploadOk = 1;

$target_file = $target_dir . $imageFileName;

/// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        header('Location: ../profiel.php?wijzig&foto=nofile', true, 302);
        $uploadOk = 0;
    }
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 1000000000) {
    //echo "Sorry, bestand is te groot!";
    header('Location: ../profiel.php?wijzig&foto=size', true, 302);
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
      header('Location: ../profiel.php?wijzig&foto=format', true, 302);
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
      header('Location: ../profiel.php?wijzig&foto=error', true, 302);
// if everything is ok, try to upload file
} else {
    if(file_exists($target_file)){
      unlink($target_file);
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $wijzigenBestandsnaam;
        //echo "Je profielfoto is geüpload!";

        $imageFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
        $imageFileName = $resultGebruikersnaam[0] . '.' . $imageFileType;
        $queryBestandsnaam="UPDATE Gebruikers SET bestandsnaam = '$imageFileName' WHERE Gebruikersnaam = '$resultGebruikersnaam[0]'";
        $wijzigenBestandsnaam = $db->query($queryBestandsnaam);

        header('Location: ../profiel.php?wijzig&foto=succes', true, 302);
exit;
    } else {
        //echo "Sorry, is is iets misgegaan, probeer het opnieuw!";
        header('Location: ../profiel.php?wijzig&foto=error', true, 302);
    }
}
?>
