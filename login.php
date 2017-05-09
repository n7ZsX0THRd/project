<?php
session_start();
include ('php/database.php');
pdo_connect();

$error = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  $email = $_POST['l_naam'];
  $wachtwoord = $_POST['l_wachtwoord'];

  $data= $db->query("SELECT TOP(1) wachtwoord FROM Gebruikers WHERE emailadres='$email'");

  $result = $data->fetchAll();
  $Totaal = count($result);

      if($Totaal == 1)
      {
        if(password_verify($wachtwoord, $result[0]['wachtwoord']))
        {
          $_SESSION['email'] = $email;
          header('location: index.php');
        }
        $error = 1;
      }
      else
      {
        $error = 1;
      }
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
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="" action="">

          <!-- login gegevens -->
          <div class="login">
              <?php if($error==1) {?>
              <p class="bg-danger">Informatie onjuist</p>
              <?php }?>
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
                <label for="remember"  class="padding-top"><input id="remember" name="remember" type="checkbox"> Remember me</label>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <button type="submit" class="btn btn-orange align-right" >Sign in</button>
              </div>
            </div>
          </div>
          <!-- Einde login knop en remember me -->

          <div class="row">
              <div class="col-lg-12">
                <p class="sub-text-register">Nog geen account? <a href="registreer.php">Registreer dan hier</a></p>
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
<script>
/*
$("li.toggle-sub").click(function(evt) {

  evt.preventDefault();
  $(this).children("span").toggleClass('glyphicon-menu-right');
  $(this).children("span").toggleClass('glyphicon-menu-down');
  $(this).children(".sub").toggle();
});
*/
</script>
</body>
</html>
