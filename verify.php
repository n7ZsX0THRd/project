<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

if(isset($_GET['gebruikersnaam']) && !empty($_GET['gebruikersnaam']) && isset($_GET['code']) && !empty($_GET['code'])){
    // Verify data
    $gebruikersnaam = $_GET['gebruikersnaam'];
    $code = $_GET['code'];
}else{
    // Invalid approach
    header ('location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ('php/includes/default_header.php');
        ?>

        <title>Verifieer - Eenmaal Andermaal</title>
    </head>

    <body>
        <?php
        include ('php/includes/header.php');
        ?>

        <div class="container">
          <div class="panel panel-default">
              <div class="panel-heading">
                  <h4 class="panel-titel">Account Registratie</h4>
              </div>
              <div class="panel-body">
              <?php
                  $dbs = $db->prepare("SELECT activatiecode FROM Activatiecodes WHERE gebruikersnaam=? AND GETDATE() < verloopdatum");
                  $dbs->execute(array($gebruikersnaam));
                  $result = $dbs->fetchAll()[0];
                  
                  if(isset($result[0])) {
                      if ($result[0] == $code) {
                          
                          //If new email was given change old to new
                          $dbs = $db->prepare("SELECT emailadres FROM Activatiecodes WHERE gebruikersnaam=?");
                          $dbs->execute(array($gebruikersnaam));
                          $newmail = $dbs->fetchAll()[0];
                          if(isset($newmail[0])) {
                              $dbs = $db->prepare("UPDATE Gebruikers SET emailadres=?");
                              $dbs->execute(array($newmail[0]));
                          }

                          //Delete verificationcode and set status to active
                          $dbs = $db->prepare("DELETE FROM Activatiecodes WHERE gebruikersnaam=? UPDATE Gebruikers SET statusID=2 WHERE gebruikersnaam=?");
                          $dbs->execute(array($gebruikersnaam, $gebruikersnaam));
                          echo 'Je account is geactiveerd';
                          header( "refresh:3;url=login.php" );
                      }else {
                          echo 'Je code klopt niet!';
                      }
                  }else {
                      echo 'Je activatiecode is verlopen.';
                  }

              ?>
              </div>
          </div>
        </div>

        <?php
        include ('php/includes/footer.php');
        ?>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>
