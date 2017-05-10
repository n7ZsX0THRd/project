<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

if(isUserLoggedIn($db))
  header('location: index.php');

$secret_questions = $db->query("SELECT ID,vraag FROM GeheimeVragen ORDER BY vraag ASC");

$required_register_fields = Array (
  'r_username' => 'Gebruikersnaam',
  'r_firstname' => 'Voornaam',
  'r_lastname' => 'Achternaam',
  'r_birthday' => 'Geboortedag',
  'r_birthmonth' => 'Geboortemaand',
  'r_birthyear' => 'Geboortejaar',
  'r_street_name' => 'Straat',
  'r_street_nr' => 'Huisnummer',
  'r_zipcode' => 'Postcode',
  'r_city' => 'Plaats',
  'r_phonenumber' => 'Telefoonnummer',
  'r_email' => 'Email',
  'r_email_confirm' => 'Email bevestiging',
  'r_password' => 'Wachtwoord',
  'r_password_confirm' => 'Wachtwoord bevestiging',
  'r_secret_question' => 'Geheime vraag',
  'r_secret_question_answer' => 'Geheime vraag antwoord',
  'r_terms_of_use' => 'Gebruikersvoorwaarden'
);


if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  $missing = array();

  foreach($required_register_fields as $key => $value)
  {
    if(isset($_POST[$key]) === false || empty($_POST[$key]))
      array_push($missing, $value);
  }
  if(count($missing) > 0)
  {
    $_SESSION['warning']['show_required_fields_warning'] = true;
    $_SESSION['warning']['missing_fields'] = $missing;
  }
  else
  {
    if($_POST['r_password'] !== $_POST['r_password_confirm'])
    {
      $_SESSION['warning']['incorrect_passwords'] = true;
    }
    else
    {
      if($_POST['r_email'] !== $_POST['r_email_confirm'])
      {
        $_SESSION['warning']['incorrect_email'] = true;
      }
      else
      {
        if(checkdate(intval($_POST['r_birthmonth']), intval($_POST['r_birthday']), intval($_POST['r_birthyear'])) === false)
        {
          $_SESSION['warning']['invalid_birthdate'] = true;
        }
        else
        {
            $dbs = $db->prepare("SELECT TOP(1) gebruikersnaam FROM Gebruikers WHERE gebruikersnaam = ?");
            $dbs->execute(array($_POST['r_username']));

            if(count($dbs->fetchAll()) != 0)
            {
              $_SESSION['warning']['invalid_username'] = true;
            }
            else {
              $dbs = $db->prepare("SELECT TOP(1) emailadres FROM Gebruikers WHERE emailadres = ?");
              $dbs->execute(array($_POST['r_email']));

              if(count($dbs->fetchAll()) != 0)
              {
                $_SESSION['warning']['invalid_email'] = true;
              }
              else {

<<<<<<< HEAD
                Beste '.$_POST['r_username'].',

                Bedankt voor het aanmelden!
=======
                  if(create_user($_POST,$db))
                  {
                    $random = rand(100000,999999);
                    $code = create_verification_for_user(array('gebruikersnaam' => $_POST['r_username'],'verificatiecode' => $random), $db);
                    if($code != 0) {
                      $to = $_POST['r_email'];
                      $subject = "Activatie code voor EenmaalAndermaal";
                      $message= '
>>>>>>> origin/master

                      Bedankt voor het aanmelden!

                      Je account is aangemaakt, je kunt inloggen met de volgende gegevens nadat je je account hebt geverifieerd door op onderstaande link te klikken.

                      --------------------
                      Gebruikersnaam: '.$_POST['r_email'].'
                      Code: '.$code.'
                      --------------------

                      Klik op deze link om je account te activeren:
                      http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$_POST['r_username'].'&code='.$code.'

                      '; //Bovenstaand bericht is de email die gebruikers ontvangen.

                      $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
                      mail($to, $subject, $message, $headers);
                    }
                    $_SESSION['email'] = $_POST['r_email'];
                    header('location: index.php');
                  }
              }
            }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Registreren - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
  </head>
  <body>

    <?php include 'php/includes/header.php' ?>
<div class="container">
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 loginscherm">
          <h1>Registreren</h1>
          <p>Welkom op de beste veilingsite van Nederland</p>
            <div>
            <form class="form-horizontal" method="post" enctype="multipart/form-data" action="">
            <div class="login">
                  <?php
                    if(isset($_SESSION['warning']['show_required_fields_warning']) && $_SESSION['warning']['show_required_fields_warning'] === true){
                  ?>
                    <div class="bg-danger" style="padding:5px;margin-bottom:5px;">Je mist een aantal velden:
                      <ul>
                      <?php
                        foreach($_SESSION['warning']['missing_fields'] as $value)
                        {
                          echo '<li>'.$value.'</li>';
                        }
                      ?>
                      </ul>
                    </div>
                  <?php
                }
                else if(isset($_SESSION['warning']['incorrect_passwords']) && $_SESSION['warning']['incorrect_passwords'] === true)
                {
                ?>
                  <p class="bg-danger">De opgegeven wachtwoorden voldoen niet aan de eisen of komen niet overeen</p>
                <?php
                }
                else if(isset($_SESSION['warning']['incorrect_email']) && $_SESSION['warning']['incorrect_email'] === true)
                {
                ?>
                  <p class="bg-danger">De opgegeven emailadressen voldoen niet aan de eisen of komen niet overeen</p>
                <?php
                }
                else if(isset($_SESSION['warning']['invalid_birthdate']) && $_SESSION['warning']['invalid_birthdate'] === true)
                {
                ?>
                  <p class="bg-danger">De opgegeven geboortedatum is ongeldig</p>
                <?php
                }
                else if(isset($_SESSION['warning']['invalid_username']) && $_SESSION['warning']['invalid_username'] === true)
                {
                ?>
                  <p class="bg-danger">De opgegeven gebruikersnaam is al in gebruik</p>
                <?php
                }
                else if(isset($_SESSION['warning']['invalid_email']) && $_SESSION['warning']['invalid_email'] === true)
                {
                ?>
                  <p class="bg-danger">Het opgegeven emailadres is al in gebruik</p>
                <?php
                }
                ?>
                  <!-- Gebruiker gegevens -->
                  <div class="input-group">
                      <div class="input-group-addon "><span class="glyphicon glyphicon-user" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="text" class="form-control" id="Tel" name="r_username" value="<?php if(isset($_POST['r_username'])){ echo $_POST['r_username']; } ?>" placeholder="Gebruikersnaam">
                      <input type="text" class="form-control" id="Voornaam" name="r_firstname" value="<?php if(isset($_POST['r_firstname'])){ echo $_POST['r_firstname']; } ?>" placeholder="Voornaam">
                      <input type="text" class="form-control" id="Achternaam" name="r_lastname" value="<?php if(isset($_POST['r_lastname'])){ echo $_POST['r_lastname']; } ?>" placeholder="Achternaam">
                      <select class="form-control" style="max-width:30%" name="r_birthday">
                        <option <?php if(isset($_POST['r_birthday']) === false){ echo 'selected'; } ?> disabled>Dag</option>
                        <?php for ($i = 1; $i <= 31; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?php if(isset($_POST['r_birthday']) && $_POST['r_birthday'] == $i){  echo 'selected'; } ?>><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                      <select class="form-control" style="max-width:30%" name="r_birthmonth">
                        <option <?php if(isset($_POST['r_birthmonth']) === false){ echo 'selected'; } ?> disabled>Maand</option>
                        <?php
                        $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
                        $index = 1;
                        foreach ($months as $value)
                        {
                          ?>
                            <option value="<?php echo $index; ?>" <?php if(isset($_POST['r_birthmonth']) && $_POST['r_birthmonth'] == $index){  echo 'selected'; } ?>><?php echo $value; ?></option>
                        <?php
                          $index++;
                        }
                        ?>
                      </select>
                      <select class="form-control" style="max-width:40%" name="r_birthyear">
                        <option <?php if(isset($_POST['r_birthyear']) === false){ echo 'selected'; } ?> disabled>Jaar</option>
                        <?php for ($i = date("Y"); $i >= 1900; $i--) { ?>
                            <option value="<?php echo $i; ?>" <?php if(isset($_POST['r_birthyear']) && $_POST['r_birthyear'] == $i){  echo 'selected'; } ?>><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                  </div>
                  <!-- Einde gebruiker gegevens -->


                  <!-- Adres gegevens -->
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></div>
                      <input style="max-width:58%" type="adres" class="form-control" id="Adres" name="r_street_name" value="<?php if(isset($_POST['r_street_name'])){ echo $_POST['r_street_name']; } ?>" placeholder="Straat">
                      <input style="max-width:20%" type="Number" class="form-control" id="Nummer" name="r_street_nr" value="<?php if(isset($_POST['r_street_nr'])){ echo $_POST['r_street_nr']; } ?>" placeholder="Nr.">
                      <input style="max-width:22%" type="text" class="form-control" id="Nummer" name="r_street_addition" value="<?php if(isset($_POST['r_street_addition'])){ echo $_POST['r_street_addition']; } ?>" placeholder="Toev.">
                      <input type="" class="form-control" id="Postcode" placeholder="Postcode" name="r_zipcode" value="<?php if(isset($_POST['r_zipcode'])){ echo $_POST['r_zipcode']; } ?>" pattern="[1-9][0-9]{3}\s?[a-zA-Z]{2}">
                      <input type="text" class="form-control" id="City" placeholder="Plaats" name="r_city" value="<?php if(isset($_POST['r_city'])){ echo $_POST['r_city']; } ?>">
                  </div>
                  <!-- einde adres gegevens -->

                  <!-- Telefoonnummer -->
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></div>
                      <input type="" minlength="10" maxlength="10" class="form-control" id="Tel" name="r_phonenumber" value="<?php if(isset($_POST['r_phonenumber'])){ echo $_POST['r_phonenumber']; } ?>" placeholder="Telefoonnummer">
                  </div>
                  <!-- Einde telefoonnummer -->

                  <!-- Email -->
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="email" class="form-control" id="inputEmail" name="r_email" value="<?php if(isset($_POST['r_email'])){ echo $_POST['r_email']; } ?>" placeholder="Email">
                      <input type="email" class="form-control" id="inputEmail" name="r_email_confirm" value="<?php if(isset($_POST['r_email_confirm'])){ echo $_POST['r_email_confirm']; } ?>" placeholder="Bevestig email">
                  </div>
                  <!-- Einde email -->

                  <!-- Wachtwoord -->
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                      <input type="password" class="form-control" id="inputPassword" name="r_password" placeholder="Wachtwoord">
                    <input type="password" class="form-control" id="inputPassword" name="r_password_confirm" placeholder="Bevestig wachtwoord">
                  </div>
                  <!-- Einde wachtwoord -->

                  <!-- Geheime vraag -->
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></div>
                      <select class="form-control" name="r_secret_question">
                        <option <?php if(isset($_POST['r_secret_question']) === false){ echo 'selected'; } ?> disabled>Geheime vraag</option>
                        <?php
                          while ($row = $secret_questions->fetch()){
                        ?>
                          <option value="<?php echo $row['ID'];?>" <?php if(isset($_POST['r_secret_question']) && $_POST['r_secret_question'] == $row['ID']){  echo 'selected'; } ?>><?php echo $row['vraag'];?></option>
                        <?php
                          }
                        ?>
                      </select>
                    <input type="text" class="form-control" id="Antwoord" name="r_secret_question_answer" value="<?php if(isset($_POST['r_secret_question_answer'])){ echo $_POST['r_secret_question_answer']; } ?>" placeholder="Antwoord">
                  </div>
                  <!-- Einde geheime vraag -->
            </div>

            <div class="bevestig">
                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8" style="position:relative;">
                        <label for="accept"  class="padding-top"><input id="accept" name="r_terms_of_use" type="checkbox" <?php if(isset($_POST['r_terms_of_use']) && $_POST['r_terms_of_use'] === 'on'){ echo 'checked'; } ?>> Akkoord met voorwaarden</label>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <button type="submit" class="btn btn-orange align-right" >Registreer</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                  <p class="sub-text-register">Al een account? <a href="login.php">Log dan hier in</a></p>
                </div>
            </div>
        </form>
        </div>
        </div>
      </div>
    </div>

<?php include 'php/includes/footer.php' ?>





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
<?php
$_SESSION['warning'] = null;
?>
