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


$page = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(isset($_POST["controle"]) && !empty($_POST["controle"])){
        if ($_POST["controle"]=="creditcard"){
          $page='bevestigd';
        } else if ($_POST["controle"]=="post"){
          $page='verwerking';
        }
  } else {

    if(isset($_POST['page']) && !empty($_POST['page'])){
      $page=$_POST['page'];
    }

  }

}

$page = 'bevestigd';



 if(!isUserLoggedIn($db))
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

        <title>Word Verkoper - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">

  </head>
  <body>

<?php
  include 'php/includes/header.php';
  // Include navigation
?>
<main class="container">
<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 loginscherm">
<div class="form-horizontal" >
  <h2>Verkoper worden</h2>
  <?php 
  switch ($page) {
    case 'registreren':
    ?>
          <!-- login gegevens -->
          <div class="login">
            <form action="activatie.php" method="POST">
              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon-piggy-bank" aria-hidden="true" background="#f0f0f0"></span></div>
                  <input type="text" pattern="^[A-Za-z_ ]{1,15}$" class="form-control" id="bank" name="bank" placeholder="Bank"  required>
              </div>
              <br>
              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon glyphicon-credit-card" aria-hidden="true"></span></div>
                  <input type="text" pattern="[0-9]{13,16}" class="form-control" id="creditcardnummer" placeholder="Creditcard nummer" name="creditcardnummer"  required>
                  <!-- test with http://www.getcreditcardnumbers.com/ -->
                  <br>
              </div>
              <br>
            </div>


          <div class="row">
            <label class="col-lg-12">Controle optie</label>
              <input class="col-lg-4 col-md-4 col-sm-4" type="radio" name="controle" value="post" required> Post<br>
              <input class="col-lg-4 col-md-4 col-sm-4" type="radio" name="controle" value="creditcard"> Creditcard<br>
          </div>

            <div class="bevestig">
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <button type="submit" value="Submit" class="btn btn-orange align-right">Word verkoper</button>
                  <br>
                </div>
              </div>
              <br>
            </form>
          </div>
        <?php
        break;
        case 'activeren':
          ?>
          <p>Vul hier de code in die je via post hebt ontvangen</p>
          <div class="input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                <input type="text" pattern="[0-9]{6}" class="form-control" id="code" placeholder="Code" name="code" value="">
            </div>

          <div class="bevestig">
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <button type="submit" class="btn btn-orange align-right">Bevestig code</button>
                
              </div>
            </div>
            <br>
          </div>
          <?php
          // activatie code invoeren
        break;
        case 'bevestigd':
          ?>
          <h3>Succes!</h3>
          <p>Uw account is geregistreerd als verkoper account! <br> 
          <a href="veilingtoevoegen.php">Begin een veiling</a>
          <br></p>
 
          <?php
          // activatie code invoeren
        break;
        case 'verwerking':
          ?>
          <h3>Succes!</h3>
          <p>Uw aanvraag is ontvangen, de gegevens worden gecontroleerd. binnenkort ontvangt u een brief met instructies om het account te activeren.<br> 
          <br></p>
 
          <?php
          // activatie code invoeren
        break;
        default:
          // keuze
          ?>
           <div class="input-group">
              <label>Ik wil: </label>
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <form action="" method="POST">
                    <input type="hidden" name="page" value="registreren">
                    <button type="submit" class="btn btn-niagara">Verkoper worden</button>
                  </form>
                </div>
              </div>
              <br>
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                  <form action="" method="POST">
                    <input type="hidden" name="page" value="activeren">
                    <button type="submit" class="btn btn-orange">Mijn code controleren</button>
                  </form>
                </div>
              </div>
              <br>
            </div>
            <?php
          }
        ?>

        </div>
        </div>
        </main>
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
