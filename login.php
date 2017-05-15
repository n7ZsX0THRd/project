<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();



$error = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  $email = $_POST['l_naam'];
  $wachtwoord = $_POST['l_wachtwoord'];

  $data= $db->query("SELECT TOP(1) wachtwoord,statusID FROM Gebruikers WHERE emailadres='$email'");

  $result = $data->fetchAll();
  $Totaal = count($result);

      if($Totaal == 1)
      {
        if(password_verify($wachtwoord, $result[0]['wachtwoord']))
        {
          if($result[0]['statusID'] !== '3'){
            $_SESSION['email'] = $email;

            if(isUserLoggedIn($db))
            {
              header('location: index.php');
            }
          }
          else
          {
            $_SESSION['warning']['user_blocked'] = true;
          }
        }
        else {
          $_SESSION['warning']['incorrect_login'] = true;
        }
      }
      else
      {
        $_SESSION['warning']['incorrect_login'] = true;
      }
  }
  else {
    if(isUserLoggedIn($db))
      header('location: index.php');
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Inloggen - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
  </head>
  <body>

<?php include 'php/includes/header.php' ?>
<div class="container">
  <div class="row">
    <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 loginscherm">
      <h1>Inloggen</h1>
      <p>Welkom op de beste veilingsite van Nederland</p>

      <div>
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="">

          <!-- login gegevens -->
          <div class="login">
              <?php if(isset($_SESSION['warning']['incorrect_login'])) {?>
                <p class="bg-danger">Informatie onjuist</p>
              <?php }else if(isset($_SESSION['warning']['user_blocked'])) {?>
                <p class="bg-danger">Gebruiker is geblokkeerd</p>
              <?php } ?>
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true" background="#f0f0f0"></span></div>
                <input type="email" class="form-control" id="inputEmail" name='l_naam' placeholder="Email">
            </div>
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                <input type="password" class="form-control" id="inputPassword" placeholder="Password" name='l_wachtwoord'>
            </div>
          </div>
          <!-- Einde login gegevens -->

          <!-- login knop en remember me -->
          <div class="bevestig">
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="position:relative;">
                <!--<label for="remember"  class="padding-top"><input id="remember" name="remember" type="checkbox"> Remember me</label>-->
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <button type="submit" class="btn btn-orange align-right">Inloggen</button>
              </div>
            </div>
          </div>
          <!-- Einde login knop en remember me -->

          <div class="row">
              <div class="col-lg-12">
                <p class="sub-text-register">Nog geen account? <a href="registreer.php">Registreer dan hier</a></p>
                <!-- <p class="sub-text-register">Wachtwoord vergeten? <a class="mousepointer" data-toggle="modal" data-target="#forgotpass">Reset je wachtwoord</a></p> -->
              </div>
          </div>
        </form>

        <!-- popup -->
        <form name="forgotpass" method="post" enctype="multipart/form-data" action="">
          <input type="hidden" name="form_name" value="changepassword"/>
        <div id="forgotpass" class="modal fade" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- popup content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Nieuwe wachtwoord aanvragen</h4>
              </div>
              <div class="modal-body">
                <?php
                if(isset($_SESSION['warning']['incorrect_pw']) && $_SESSION['warning']['incorrect_pw'] === true)
                {
                ?>
                  <p class="bg-danger" style="padding: 5px;">Dit emailadres bestaat niet</p>
                <?php
                }
                //pw_not_equal
                ?>
                  <div class="form-group">
                    <div class="form-group">
                      <label for="formpass">Emailadres</label>
                      <input name="passchange" type="email" class="form-control" id="formpass" placeholder="Email">
                    </div>

                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleer</button>
                <button type="submit" class="btn btn-orange">Verder</button>
              </div>
            </div>
          </div>
        </div>

        </form>


        <form name="forgotpass" method="post" enctype="multipart/form-data" action="">
          <input type="hidden" name="form_name" value="changepassword"/>
        <div id="forgotpass" class="modal fade" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- popup content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Nieuwe wachtwoord aanvragen</h4>
              </div>
              <div class="modal-body">
                <?php
                if(isset($_SESSION['warning']['incorrect_pw']) && $_SESSION['warning']['incorrect_pw'] === true)
                {
                ?>
                  <p class="bg-danger" style="padding: 5px;">Dit emailadres bestaat niet</p>
                <?php
                }
                //pw_not_equal
                ?>
                  <div class="form-group">
                    <div class="form-group">
                      <label for="formpass">Emailadres</label>
                      <input name="passchange" type="email" class="form-control" id="formpass" placeholder="Email">
                    </div>

                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleer</button>
                <button type="button" class="btn btn-orange data-toggle="modal" data-target="#question"">Verder</button>
              </div>
            </div>
          </div>
        </div>
        </form>

        <form name="question" method="post" enctype="multipart/form-data" action="">
          <input type="hidden" name="form_name" value="changepassword"/>
        <div id="question" class="modal fade" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- popup content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Nieuwe wachtwoord aanvragen</h4>
              </div>
              <div class="modal-body">
                <?php
                if(isset($_SESSION['warning']['incorrect_pw']) && $_SESSION['warning']['incorrect_pw'] === true)
                {
                ?>
                  <p class="bg-danger" style="padding: 5px;">Dit emailadres bestaat niet</p>
                <?php
                }
                //pw_not_equal
                ?>
                  <div class="form-group">
                    <div class="form-group">
                      <label for="formpass">Emailadres</label>
                      <input name="passchange" type="email" class="form-control" id="formpass" placeholder="Email">
                    </div>

                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleer</button>
                <button type="submit" class="btn btn-orange">Verder</button>
              </div>
            </div>
          </div>
        </div>
        </form>













      </div>
    </div>
  </div>
</div>
<?php include 'php/includes/footer.php' ?>
</div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
<?php
  $_SESSION['warning'] = null;
?>
