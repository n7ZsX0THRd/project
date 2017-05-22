<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$error = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

if($_POST['form_name']=='requestanswer'){
  $antwoord= $db->prepare("SELECT TOP(1) antwoordtekst FROM Gebruikers WHERE emailadres=?");
  $antwoord->execute(array($_GET['email']));
  $antwoordquery = $antwoord->fetchAll();

      if(password_verify($_POST['antwoord'], $antwoordquery[0]['antwoordtekst']))
      {
        function RandomString()
        {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randstring = '';
            for ($i = 0; $i < 10; $i++) {
                $randstring = $randstring.$characters[rand(0, strlen($characters))];
            }
            return $randstring;
        }
          $randomkey = RandomString();
          $nieuweww = $db->prepare("UPDATE Gebruikers SET wachtwoord=? WHERE emailadres=?");
          $nieuweww->execute(array(password_hash($randomkey, PASSWORD_DEFAULT), $_POST['emailww']));
          print($randomkey);
          $_SESSION['warning']['wrong'] = false;
        }
        else {
          $_SESSION['warning']['wrong'] = true;
        }

}else if($_POST['form_name']=='login'){
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
 }

if($_SERVER['REQUEST_METHOD'] == 'GET'){
 //email checken in de database


 if (isset($_GET['email'])){
   $emailforget = $_GET['email'];
   $emailgebruiker = $db->prepare("SELECT emailadres FROM Gebruikers WHERE emailadres = ?");
   $emailgebruiker->execute (array($emailforget));
   $emailcheck = $emailgebruiker->fetchAll();

   if(count($emailcheck) == 1)
   { print('poep1');
     $_SESSION['warning']['invalidmail'] = true;
   }else{
     print('poep2');
     $_SESSION['warning']['invalidmail'] = false;
   }
 }

}

 if(isUserLoggedIn($db))
   header('location: index.php');

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
          <input type="hidden" name="form_name" value="login"/>

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
                <p class="sub-text-register">Wachtwoord vergeten? <a class="mousepointer" data-toggle="modal" data-target="#forgotpass">Reset je wachtwoord</a></p>
              </div>
          </div>
        </form>

        <!-- popup -->
        <form name="forgotpass" method="get" enctype="multipart/form-data" action="">
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
                if(isset($_SESSION['warning']['invalidmail']) && $_SESSION['warning']['invalidmail'] == false)
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
                      <input name="email" type="email" class="form-control"  placeholder="Email">
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



      <?php if (isset($_GET['email'])==true){  ?>
      <form name="question" method="post" enctype="multipart/form-data" action="">
        <input type="hidden" name="emailww" value="<?php echo $_GET['email']; ?>"/>
        <input type="hidden" name="form_name" value="requestanswer"/>
        <div id="question" class="modal fade" role="dialog">
          <div class="modal-dialog modal-sm">
            <!-- popup content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Geheime vraag</h4>
              </div>
              <div class="modal-body">
                <?php
                //Geheime vraag ophalen van gebruiker
                $emailforget = $_GET['email'];
                $secret_question = $db->prepare("SELECT V.vraag AS vraag FROM GeheimeVragen AS V inner join Gebruikers AS G on G.vraag  = V.ID WHERE emailadres = ?");
                $secret_question->execute (array($emailforget));
                $vraag = $secret_question->fetchAll()[0];
                ?>
                  <div class="form-group">
                    <div class="form-group">
                      <label for="formpass"><?php echo $vraag['vraag'];?></label>
                      <input name="antwoord" type="text" class="form-control" id="formpass" placeholder="Antwoord">
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuleer</button>
                <button type="submit" class="btn btn-orange">Verstuur</button>
              </div>
            </div>
          </div>
        </div>
        </form>
        <?php } ?>
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
<?php if(isset($_GET['email']) && isset($_SESSION['warning']['invalidmail'])  && $_SESSION['warning']['invalidmail'] == true){ ?>
<script type="text/javascript">
         $(window).load(function(){
             $('#question').modal('show');
         });
</script>

<?php
}else if(isset($_GET['email']) && isset($_SESSION['warning']['invalidmail'])){
?>
<script type="text/javascript">
           $(window).load(function(){
               $('#forgotpass').modal('show');
           });
</script>
  <?php
  }
  ?>
</body>
</html>

<?php

$_SESSION['warning'] = null;
?>
