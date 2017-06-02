<?php
/*
  iProject Groep 2
  30-05-2017

  file: login.php
  purpose:
  Login user
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// Include database, and include user functions.
// Connect to database

$error = 0;

// If request to login
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  if($_POST['form_name']=='requestanswer'){
      // If request post is get reset password

      // Get answer for secret question
      $antwoord= $db->prepare("SELECT TOP(1) antwoordtekst FROM Gebruikers WHERE emailadres=?");
      $antwoord->execute(array($_GET['email']));
      $antwoordquery = $antwoord->fetchAll();

      // Check if given answer is equal to hashed answer in database
      if(password_verify($_POST['antwoord'], $antwoordquery[0]['antwoordtekst']))
      {
          // Generate randomstring of 10 characters.
          function RandomString()
          {
              $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $randstring = '';
              for ($i = 0; $i < 10; $i++) {
                  $randstring = $randstring.$characters[rand(0, strlen($characters)-1)];
              }
              return $randstring;
          }
          // Genereate random password for user
          $randomkey = RandomString();

          $usernamequery =$db->prepare("SELECT gebruikersnaam FROM Gebruikers WHERE emailadres=?");
          $usernamequery->execute(array($_POST['emailww']));
          $username = $usernamequery->fetchAll()[0];
          //Get username from the user who requested a new password.

          $nieuweww = $db->prepare("UPDATE Gebruikers SET wachtwoord=? WHERE emailadres=?");
          $nieuweww->execute(array(password_hash($randomkey, PASSWORD_DEFAULT), $_POST['emailww']));
          // Insert new password into database

          sendMail($_POST['emailww'],'Nieuw wachtwoord','<table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
              <tr>
                  <td style="color:#023042">
                      Beste '.$username['gebruikersnaam'].'
                  </td>
              </tr>
              <tr>
                  <td style="padding: 20px 0 0 0; color:#023042">
                      <p>Er is een nieuw wachtwoord aangevraagd voor dit account.</p>
                  </td>
              </tr>
              <tr>
                  <td style="padding: 20px 0 0 0; color:#023042">
                      <p>Dit is uw nieuwe wachtoord '.$randomkey.'</p>
                      <p><a href="http://iproject2.icasites.nl/login.php?email_input='.$_POST['emailww'].'">Log nu hier in</a></p>
                  </td>
              </tr>
              <tr>
                  <td style="padding: 20px 0 0 0; color:#023042">
                      <p>Wijzig dit wachtwoord zo snel mogelijk op uw profielpagina.</p>
                  </td>
              </tr>
              <tr>
                  <td style="padding: 20px 0 20px 0; color:#023042">
                      <p>Met vriendelijke groeten,<br>Team EenmaalAndermaal</p>
                  </td>
              </tr>
          </table>');
          // Send email with new key to user.

          // Set warning invalidanswer session to false. Show Message if answer is correct
          $_SESSION['warning']['invalidanswer'] = false;
        }
        else {
          // Set warning invalidanswer session to true. Show Error if answer is incorrect
          $_SESSION['warning']['invalidanswer'] = true;
        }

  }else if($_POST['form_name'] == 'login'){

   //echo 'LOGIN';
   //var_dump($_POST);
   // If requested post = login;

   $email = $_POST['l_naam'];
   $wachtwoord = $_POST['l_wachtwoord'];
   // Get email and password from sign in

   // Select user where email is given emaildress;
   $data= $db->query("SELECT TOP(1) wachtwoord,statusID FROM Gebruikers WHERE emailadres='$email'");

   $result = $data->fetchAll();
   $Totaal = count($result);
   echo $Totaal;
   if($Totaal == 1) // If user found, verify password...
   {
     if(password_verify($wachtwoord, $result[0]['wachtwoord'])) // Check if password is valid
     {

       if($result[0]['statusID'] !== '3'){// Check if user is not blocked
         $_SESSION['email'] = $email;

         if(isUserLoggedIn($db)) // Check if user is succesfully logged in
         {
           header('location: index.php');
           // Redirect to index when logged in succesfully
         }
       }
       else
       {
         $_SESSION['warning']['user_blocked'] = true;
         // user is blocked, set session to show wanring for blocked user
       }
     }
     else {
       // password email combination is invalid, set session to show warning for incorrect login
       $_SESSION['warning']['incorrect_login'] = true;
     }
   }
   else
   {
     // emailadress is not found in database, set session to show warning for incorrect login
     $_SESSION['warning']['incorrect_login'] = true;
   }
  }
}

if($_SERVER['REQUEST_METHOD'] == 'GET'){
 //User requested to reset password

 if (isset($_GET['email'])){ // Check if emailadress isset


   // Query to select user with emailadres given
   $emailgebruiker = $db->prepare("SELECT emailadres FROM Gebruikers WHERE emailadres = ?");
   $emailgebruiker->execute (array($_GET['email']));
   $emailcheck = $emailgebruiker->fetchAll();


   // Check if email exists in database
   if(count($emailcheck) == 1)
   {
     // Set session invalidmail to true, and show warning with invalid mail
     $_SESSION['warning']['invalidmail'] = true;
   }else{
     // Set session invalidmail to false, and show popup with an input field to give the answer for
     // secret question.
     $_SESSION['warning']['invalidmail'] = false;
   }
 }

}

 if(isUserLoggedIn($db))
   header('location: index.php');
  // If the user is logged In, redirect to homepage.
  // No purpose to get on this page

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php
          include 'php/includes/default_header.php';
          // Include default head
        ?>

        <title>Inloggen - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">

  </head>
  <body>

<?php
  include 'php/includes/header.php';
  // Include navigation
?>
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
              <?php }
              if(isset($_SESSION['warning']['invalidanswer']) && $_SESSION['warning']['invalidanswer'] == false)
              {
              ?>
                <p class="bg-success" style="padding: 5px;">Nieuwe wachtwoord is verstuurd</p>
              <?php
              }?>
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true" background="#f0f0f0"></span></div>
                <input type="email" class="form-control" id="inputEmail" name='l_naam' placeholder="Email" value="<?php if (isset($_GET['email_input'])){ echo $_GET['email_input']; }?>">
            </div>
            <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="l_wachtwoord" value="">
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
                // If session email invalid show warning
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
                // If session invalidanswer show warning
                if(isset($_SESSION['warning']['invalidanswer']) && $_SESSION['warning']['invalidanswer'] == true)
                {
                ?>
                  <p class="bg-danger" style="padding: 5px;">Antwoord onjuist</p>
                <?php
                }
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
<?php
if(isset($_GET['email']) || (isset($_SESSION['warning']['invalidmail'])  && $_SESSION['warning']['invalidmail'] == true)){
  // Check if email is valid
  if(isset($_SESSION['warning']['invalidanswer'])==false || $_SESSION['warning']['invalidanswer'] ==true){
  // Show popup with question + answer + notifcation for wrong answer
?>
    <script type="text/javascript">
             $(window).load(function(){
                 $('#question').modal('show');
             });
    </script>
  <?php
  }
}else if(isset($_GET['email']) && isset($_SESSION['warning']['invalidmail'])){
  // If email is invalid, show popup with wrong email
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
