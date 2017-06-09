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

if(isUserLoggedIn($db) == false)
  header("Location: index.php");

$result = getLoggedInUser($db);

if($result['typegebruiker'] == 2 || $result['typegebruiker'] == 3)
{
  header("Location: veilingtoevoegen.php");
  exit();
}


//echo var_dump($user);
$error = 0;


$page = '';


if ($result['typegebruiker'] == 2){
  $page = 'alVerkoper';
} else if ($result["statusID"]==1){ //Als de gebruier inactief is
  $page = 'inactief';
} else if($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(isset($_POST["page"]) && !empty($_POST["page"])){

      $page=$_POST['page'];

      if ($_POST["page"]=="creditcard"){

        $creditcardnummer=htmlspecialchars($_POST["creditcardnummer"]);
        $banknaam=htmlspecialchars($_POST["bank"]);
        $data = $db->prepare("  INSERT INTO Verkopers (gebruikersnaam, banknaam, rekeningnummer, creditcardnummer, controleoptienaam)
                                VALUES (?, ?, 'NVT', ?, 'creditcard');
                            ");

        $data->execute(array($result['gebruikersnaam'], $banknaam, $creditcardnummer));

        $data = $db->prepare("  UPDATE Gebruikers
                                SET typegebruiker = 2
                                WHERE gebruikersnaam = ?;
                            ");

        $data->execute(array($result['gebruikersnaam']));

        $page='bevestigd';
      } else if ($_POST["page"]=="post"){

        $bankrekeningnummer=htmlspecialchars($_POST["bankrekeningnummer"]);
        $banknaam=htmlspecialchars($_POST["bank"]);

        $data = $db->prepare("  INSERT INTO Verkopers (gebruikersnaam, banknaam, rekeningnummer, creditcardnummer, controleoptienaam)
                                VALUES (?, ?, ?, 'NVT', 'post');
                            ");

        $data->execute(array($result['gebruikersnaam'], $banknaam, $bankrekeningnummer));

        $data = $db->prepare("  UPDATE Gebruikers
                                SET typegebruiker = 4
                                WHERE gebruikersnaam = ?;
                            ");

        $data->execute(array($result['gebruikersnaam']));

        $page='verwerking';
      }

      if ($_POST["page"]=="code-controle"){
        $codeCorrect=false;
        if(isset($_POST["code"]) && !empty($_POST["code"])){
          $ingevoerdeCode=htmlspecialchars($_POST["code"]);

          $data = $db->prepare("  SELECT TOP(1) activatiecode FROM Verkopers WHERE gebruikersnaam = ?;
                            ");

          $data->execute(array($result['gebruikersnaam']));
          $result=$data->fetchAll();
          if (isset($ingevoerdeCode) && isset($result[0]['activatiecode'])){
            if ($ingevoerdeCode==$result[0]['activatiecode']){
              $page='bevestigd';

              $data = $db->prepare("
                                    UPDATE Verkopers
                                    SET activatiecode = NULL, startdatum = NULL
                                    WHERE gebruikersnaam= ?;
                                ");

              $data->execute(array($result['gebruikersnaam']));

              $data = $db->prepare("
                                    UPDATE Gebruikers
                                    SET typegebruiker = 2
                                    WHERE gebruikersnaam= ?;
                                ");

              $data->execute(array($result['gebruikersnaam']));
            }
          }else{
            $page='onjuiste-code';
          }
        }
      }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php
          include 'php/includes/default_header.php';
          // Include default head
        ?>

        <title>Verkoper worden - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/formulier.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script>
        $(document).ready(function(){
            $(".creditcard").click(function(){
              $("#input-creditcard").show();
                $("#input-post").hide();
            });
            $(".post").click(function(){
                $("#input-post").show();
                $("#input-creditcard").hide();
            });
        });

        jQuery(function ($) {
            var $inputs = $('input[name=bankrekeningnummer],input[name=creditcardnummer]');
            $inputs.on('input', function () {
                // Set the required property of the other input to false if this input is not empty.
                $inputs.not(this).prop('required', !$(this).val().length);
            });
        });
        </script>

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
            <form action="" method="POST">

            <div class="row">
                <div class="col-lg-12">
                    <strong>Controle optie</strong><br>
                    <i>Deze gegevens zijn nodig om uw identiteit te controleren</i>
                </div>
                <div class="col-lg-12 col-xs-12">
                  <label class="post">
                    <input type="radio" name="page" value="post" checked> Post
                  </label>
                  <br/>
                  <label class="creditcard">
                    <input type="radio" name="page" value="creditcard"> Creditcard
                  </label>
                  <br/>
                </div>
            </div>

              <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon-piggy-bank" aria-hidden="true" background="#f0f0f0"></span></div>
                  <input type="text" pattern="^[A-Za-z_ ]{1,15}$" class="form-control" id="bank" name="bank" placeholder="Bank"  required>
              </div>
              <br>
              <div  id="input-creditcard"  style="display:none;" class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon glyphicon-credit-card" aria-hidden="true"></span></div>
                  <input type="text" pattern="[0-9]{13,16}" class="form-control" id="creditcardnummer" placeholder="Creditcard nummer" name="creditcardnummer" value=""  required>
                  <!-- test with http://www.getcreditcardnumbers.com/ -->
                  <br>
              </div>
              <div  id="input-post"  class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon glyphicon-credit-card" aria-hidden="true"></span></div>
                  <input type="text" pattern="[a-zA-Z0-9]{6,34}" class="form-control" id="bankrekeningnummer" placeholder="Bankrekening nummer" name="bankrekeningnummer" value="" required>
                  <!-- test with http://www.getcreditcardnumbers.com/ -->
                  <br>
              </div>
              <br>
            </div>

            <div class="bevestig">
              <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-lg-offset-6 col-md-offset-6 col-sm-offset-6">
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
          <form action="" method="POST">
            <div class="input-group">
                <div class="input-group-addon"><span class="glyphicon glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                  <input type="text" pattern="[0-9]{6}" class="form-control" id="code" placeholder="Code" name="code" value="">
              </div>
              <input type="hidden" name="page" value="code-controle">
            <div class="bevestig">
              <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 col-xs-offset-6 col-lg-offset-6 col-md-offset-6 col-sm-offset-6">
                  <button type="submit" class="btn btn-orange align-right">Bevestig code</button>

                </div>
              </div>
              <br>
            </div>
          </form>
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
        case 'onjuiste-code':
          ?>
          <h3>Oops!</h3>
          <p>De code klopt niet, probeer het opnieuw<br>
          <form action="" method="POST" id="my_form">
          <!-- Your Form -->
          <input type="hidden" name="page" value="activeren">
          <a href="javascript:{}" onclick="document.getElementById('my_form').submit(); return false;">probeer het opnieuw</a>
          </form>
          <br></p>

          <?php
          // activatie code invoeren
        break;
        case 'alVerkoper':
          ?>
          <h3>&#x1F4B0 &#x1F4B0 &#x1F4B0</h3>
          <p>U bent al geregistreerd als verkoper<br>
          <br></p>

          <?php
          // activatie code invoeren
        break;
        case 'inactief':
          ?>
          <h3>Oeps!</h3>
          <p>Je hebt jouw account nog niet geverifieerd.<br>
          <a href="index.php?mail">Stuur mij opnieuw een mail!</a>
          <br></p>

          <?php
          // activatie code invoeren
        break;
        default:
          // keuze
          ?>
            <div class="row">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <p>Om verkoper te worden, heb je een code nodig.
                Vraag nu een code aan of controleer jouw code</p>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <form action="" method="POST">
                  <input type="hidden" name="page" value="registreren">
                  <button type="submit" class="btn btn-niagara" style="margin-top:0px;width:100%;margin-bottom:15px;">Code Aanvragen</button>
                </form>
              </div>
              <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <form action="" method="POST">
                  <input type="hidden" name="page" value="activeren">
                  <button type="submit" class="btn btn-orange" style="width:100%;float:right;margin-bottom:15px;">Code Controleren</button>
                </form>
              </div>
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
