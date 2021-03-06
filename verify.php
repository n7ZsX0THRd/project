<?php
/*
  iProject Groep 2
  30-05-2017

  file: verify.php
  purpose:
  Verify user account
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// include database and user functions
// connect with database

// If all get parameters are set
if(isset($_GET['gebruikersnaam']) && !empty($_GET['gebruikersnaam']) && isset($_GET['code']) && !empty($_GET['code'])){
    // Verify data
    $gebruikersnaam = $_GET['gebruikersnaam'];
    $code = $_GET['code'];
}else{
    // Invalid approach
    // redirect to homepage
    header ('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        include ('php/includes/default_header.php');
        // Include default head
        ?>

        <title>Verifieer - Eenmaal Andermaal</title>
    </head>

    <body>
        <?php
        include ('php/includes/header.php');
        // include navigation
        ?>

        <div class="container">
          <div class="panel panel-default">
              <div class="panel-heading">
                  <h4 class="panel-titel">Account Registratie</h4>
              </div>
              <div class="panel-body">
              <?php
              try {
                  $dbs = $db->prepare("SELECT activatiecode FROM Activatiecodes WHERE gebruikersnaam=? AND GETDATE() < verloopdatum");
                  $dbs->execute(array($gebruikersnaam));
                  $result = $dbs->fetchAll()[0];
                }
                catch (PDOException $e) {

                }
                  if(isset($result[0])) {

                      if ($result[0] == $code) {
                        try{
                          //If new email was given change old to new
                          $dbs = $db->prepare("SELECT emailadres,gebruikersnaam FROM Activatiecodes WHERE gebruikersnaam=?");
                          $dbs->execute(array($gebruikersnaam));
                          $newmailResult = $dbs->fetchAll();
                          if(count($newmailResult[0][0]) == 1) {
                              $dbs = $db->prepare("UPDATE Gebruikers SET emailadres=? WHERE gebruikersnaam=?");
                              $dbs->execute(array($newmailResult[0]['emailadres'],$newmailResult[0]['gebruikersnaam']));
                          }

                          //Delete verificationcode and set status to active
                          $dbs = $db->prepare("DELETE FROM Activatiecodes WHERE gebruikersnaam=? UPDATE Gebruikers SET statusID=2 WHERE gebruikersnaam=?");
                          $dbs->execute(array($gebruikersnaam, $gebruikersnaam));
                          echo 'Je account is geactiveerd';
                          header( "refresh:3;url=login.php" );

                          } catch (PDOException $e) {

                          }

                    } else {
                        echo 'Je code klopt niet!';
                        // Incorrect key
                    }
                  }else {
                      echo 'Je activatiecode is verlopen.';
                      // Key expired
                  }


              ?>
              </div>
          </div>
        </div>

        <?php
        include ('php/includes/footer.php');
        // include footer
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
