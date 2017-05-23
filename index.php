<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

if(isset($_GET['mail'])==true) {
    $dbs= $db->prepare("SELECT gebruikersnaam FROM Gebruikers WHERE emailadres=?");
            $dbs->execute(array($_SESSION['email']));
            $gebruikersnaam = $dbs->fetchAll()[0]['gebruikersnaam'];

            $random = rand(100000,999999);
              $code = update_verification_for_user(array('email' => $_SESSION['email'],'verificatiecode' => $random), $db);
              if($code != 0) {
                $to = $_SESSION['email'];
                $subject = 'Nieuwe activatiecode voor EenmaalAndermaal';
                $message = '
                <tr>
                    <td align="center" bgcolor="#FFFFFF" style="padding: 40px 30px 40px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif;">
                            <tr>
                                <td style="color:#023042">
                                    Beste '.$gebruikersnaam.',
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0 0; color:#023042">
                                    <p>Er is een nieuwe activatiecode voor je aangemaakt, je kunt inloggen met de volgende gegevens nadat je je account hebt geverifieerd door op onderstaande link te klikken.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0 0; color:#023042">
                                    <p>De link is gekoppeld aan het volgende emailadres:<br>E-mail: '.$_SESSION['email'].'</p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 0 0; color:#023042">
                                    <p>Klik op deze link om je account te activeren:<br><a href="http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'">http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'</a></p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 20px 0 20px 0; color:#023042">
                                    <p>Met vriendelijke groeten,<br>Team EenmaalAndermaal</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>';
                sendMail($to,$subject,$message);
              }
    $nieuwe_mail = 1;
}
$rubriekID = -1;
$childrenRubriekenQuery = $db->prepare("SELECT TOP(10) rubrieknummer, rubrieknaam FROM Rubriek WHERE parentRubriek = ? ORDER BY volgnr ASC, rubrieknaam ASC");
$childrenRubriekenQuery->execute(array(htmlspecialchars($rubriekID)));
$childrenRubrieken = $childrenRubriekenQuery->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Veilingsite - Eenmaal Andermaal</title>
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>

  <div id="myCarousel" class="carousel slide" data-ride="carousel" style="margin-top:-20px;">
  <!-- Indicators -->
    <ol class="carousel-indicators" style="display:none">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>
      <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
      <div class="item active">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <center>
                <h1>Eenmaal Andermaal</h1><img src="images/hamerkleur.png" width="250">
                <p>Veil nu snel al uw oude spullen, en vang er nog een leuk bedrag voor!</p>
              </center>
            </div>
          </div>
        </div>
      </div>

      <div class="item">
        <img src="chicago.jpg" alt="Chicago">
      </div>

      <div class="item">
        <img src="ny.jpg" alt="New York">
      </div>
    </div>
  </div>

    <?php if (isUserLoggedIn($db)){
        $user = getLoggedInUser($db);
        if($user['statusID'] == 1){
          ?>
  <div class="container banner-top-container">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">
          <div class="bg-warning banner-top">
              Hey <?php echo $user['voornaam']; ?>, je hebt jouw account nog niet geverifieerd. <br>
              Er is een e-mail naar <?php echo $_SESSION['email']; ?> gestuurd. <br>
              Geen e-mail gekregen? Controleer je ongewenst box of stuur een nieuwe e-mail. <br>
              <a href="?mail" type="submit" class="btn btn-orange">Stuur opnieuw!</a> <br>
              <?php
              if($nieuwe_mail == 1) {
                  echo 'Er is een nieuwe activatie e-mail verstuurd.';
              }
              ?>
              Klopt het e-mailadres niet? Wijzig deze dan <a href="profiel.php">hier</a>.

          </div>
        </div>
      </div>
    </div>
    <?php
    }
  }
    ?>
    <div class="container">
      <div class="row">
        <div class="col-md-3 col-lg-2 col-sm-4 sidebar">
          <?php
            if(isUserLoggedIn($db))
              include 'php/includes/sidebar.php';
            else {
              ?>
                <h3></h3>
                <ul class="menubar">
                  <li class="toggle-sub active">
                    <a href="">Rubrieken</a>
                  </li>
                  <ul class="sub">
                    <?php
                      foreach($childrenRubrieken as $row)
                      {
                        ?>
                        <li>
                          <a href="rubriek.php?rubriek=<?php echo $row['rubrieknummer']; ?>"><?php echo $row['rubrieknaam']; ?></a>
                        </li>
                        <?php
                      }
                    ?>
                  </ul>
                </ul>
              <?php
            }
          ?>
        </div>
        <div class="col-md-9 col-lg-10 col-sm-8">
          <div class="container-fluid content_col">
            <div class="row navigation-row">
              <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="container-fixed">
                  <div class="row item-row">
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                  </div>
                </div>
                <div class="row item-row">
                  <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                  <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                  <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                  <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                     <div class="thumbnail">
                      <h3 style="padding-left: 10px">Boeing737</h3>
                      <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                      <div class="caption captionfix">
                        <h3>COUNTDOWN</h3>
                        <p>Huidige bod: <strong>€270000.-</strong></p>
                        <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                      </div>
                     </div>
                    </div>
                </div>
              </div>
            </div>

          </div>
        </div>
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
  </body>
</html>
