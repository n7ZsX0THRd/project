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
                $subject = "Nieuwe activatie code voor EenmaalAndermaal";
                $message= '

                Beste '.$gebruikersnaam.',

                Er is een nieuwe activatiecode voor je aangemaakt, je kunt inloggen met de volgende gegevens nadat je je account hebt geverifieerd door op onderstaande link te klikken.

                --------------------
                E-mail: '.$_SESSION['email'].'
                Code: '.$code.'
                --------------------

                Klik op deze link om je account te activeren:
                http://iproject2.icasites.nl/verify.php?gebruikersnaam='.$gebruikersnaam.'&code='.$code.'

                '; //Bovenstaand bericht is de email die gebruikers ontvangen.

                $headers = 'From: noreply@iproject2.icasites.nl' . "\r\n";
                mail($to, $subject, $message, $headers);
              }
    $nieuwe_mail = 1;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Veilingsite - Eenmaal Andermaal</title>
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>


    <div class="container-fluid fullwidth-container-fix">
      <div class="row fullwidth-width-row">
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12">
          <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
              <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
              <li data-target="#myCarousel" data-slide-to="1"></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner">
              <div class="item active">
                  <div class="row">
                    <div class="col-lg-4 col-lg-offset-2">
                        <div class="row">
                          <div class="col-lg-12">
                            <h1 style="width:100%;text-align:right;">Otis FIX IETS</h1>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <img src="images/users/johndoe.jpg" style="width:90%;float:left;">
                            </div>
                            <div class="col-lg-6">
                              <p style="text-align:right;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                          <div class="col-lg-12">
                            <h1 style="width:100%;text-align:left;">Guus FIX IETS</h1>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                              <p style="text-align:left;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                            </div>
                            <div class="col-lg-6">
                                <img src="images/users/otisvdm.jpg" style="width:90%;float:right;">
                            </div>
                        </div>
                    </div>
                  </div>
              </div>

              <div class="item">
                <div class="row">
                  <div class="col-lg-4 col-lg-offset-2">
                      <div class="row">
                        <div class="col-lg-12">
                            <h3 style="text-align:right;">Titel</h3>
                        </div>
                        <div class="col-lg-4">
                          <img src="images/vliegtuig.jpg" style="max-width:100%;">
                        </div>
                        <div class="col-lg-8">
                          <p style="text-align:right;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-lg-12">
                              <h3 style="text-align:left;">Titel</h3>
                          </div>
                          <div class="col-lg-8">
                            <p style="text-align:left;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                          </div>
                          <div class="col-lg-4">
                            <img src="images/vliegtuig.jpg" style="max-width:100%;">
                          </div>
                      </div>
                  </div>
                  <div class="col-lg-4">
                      <div class="row">
                        <div class="col-lg-12">
                            <h3 style="text-align:right;">Titel</h3>
                        </div>
                        <div class="col-lg-4">
                          <img src="images/vliegtuig.jpg" style="max-width:100%;">
                        </div>
                        <div class="col-lg-8">
                          <p style="text-align:right;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-lg-12">
                              <h3 style="text-align:left;">Titel</h3>
                          </div>
                          <div class="col-lg-8">
                            <p style="text-align:left;">Dick Advocaat blijft tot en met het WK bondscoach als hij er met het Nederlands elftal in slaagt kwalificatie af te dwingen voor het eindtoernooi volgend jaar in Rusland. "Als Advocaat het met succes oppakt, is het logisch dat hij ook tijdens het WK de bondscoach is", zei Jean-Paul Decossaux dinsdag op een persconferentie in Zeist waar hij de aanstelling van Advocaat en diens assistent Ruud Gullit bevestigde.</p>
                          </div>
                          <div class="col-lg-4">
                            <img src="images/vliegtuig.jpg" style="max-width:100%;">
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Left and right controls -->
          </div>
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
              Klopt het e-mailadres niet? Wijzig deze dan <a href="profiel.php?wijzig">hier</a>.

          </div>
        </div>
      </div>
    </div>
    <?php
    }
  }
    ?>
    <div class="row">
    <div class="col-lg-10 col-lg-offset-1 col-md-12 col-sm-12 col-xs-12">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3 col-lg-2 col-sm-3 sidebar">
          <h3>Categorieën</h3>
          <ul class="menubar">
            <li>
              <a href="">Menu Item</a>
            </li>
            <li class="toggle-sub">
              <a href="">Menu Item</a>
            </li>
            <ul class="sub">
              <li>
                <a href="">Sub Item</a>
              </li>
              <li>
                <a href="">Sub Item</a>
              </li>
            </ul>
            <li class="toggle-sub">
              <a href="">Menu Item</a>
            </li>
            <ul class="sub">
              <li>
                <a href="">Sub Item</a>
              </li>
              <li>
                <a href="">Sub Item</a>
              </li>
            </ul>
            <li>
              <a href="">Menu Item</a>
            </li>
          </ul>
        </div>
        <div class="col-md-9 col-lg-10 col-sm-9">
          <div class="container-fluid content_col">
            <div class="row">
              <div class="col-lg-12 remove-margin">
                <h1>Boeken</h1>
              <div>
              <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="container-fixed">
                  <div class="row item-row">
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                      <div class="thumbnail">
                        <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                        <div class="caption captionfix">
                          <h3>Spiegel</h3>
                          <p>Huidige bod: <strong>€270.-</strong></p>
                          <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                      <div class="thumbnail">
                        <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                        <div class="caption captionfix">
                          <h3>Spiegel</h3>
                          <p>Huidige bod: <strong>€270.-</strong></p>
                          <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                      <div class="thumbnail">
                        <div class="thumb_image" style="background-image:url(images/bmw.jpg);"></div>
                        <div class="caption captionfix">
                          <h3>Spiegel</h3>
                          <p>Huidige bod: <strong>€270.-</strong></p>
                          <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                      <div class="thumbnail">
                        <div class="thumb_image" style="background-image:url(images/eten.jpg);"></div>
                        <div class="caption captionfix">
                          <h3>Spiegel</h3>
                          <p>Huidige bod: <strong>€270.-</strong></p>
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
        <div class="row">
          <div class="col-lg-12 remove-margin">
            <h1>Boeken</h1>
          <div>
          <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
            <div class="container-fixed">
              <div class="row item-row">
                <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                    <div class="caption captionfix">
                      <h3>Spiegel</h3>
                      <p>Huidige bod: <strong>€270.-</strong></p>
                      <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                    <div class="caption captionfix">
                      <h3>Spiegel</h3>
                      <p>Huidige bod: <strong>€270.-</strong></p>
                      <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <div class="thumb_image" style="background-image:url(images/bmw.jpg);"></div>
                    <div class="caption captionfix">
                      <h3>Spiegel</h3>
                      <p>Huidige bod: <strong>€270.-</strong></p>
                      <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <div class="thumb_image" style="background-image:url(images/eten.jpg);"></div>
                    <div class="caption captionfix">
                      <h3>Spiegel</h3>
                      <p>Huidige bod: <strong>€270.-</strong></p>
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
    <div class="row">
      <div class="col-lg-12 remove-margin">
        <h1>Boeken</h1>
      <div>
      <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
        <div class="container-fixed">
          <div class="row item-row">
            <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
              <div class="thumbnail">
                <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                <div class="caption captionfix">
                  <h3>Spiegel</h3>
                  <p>Huidige bod: <strong>€270.-</strong></p>
                  <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
              <div class="thumbnail">
                <div class="thumb_image" style="background-image:url(images/vliegtuig.png);"></div>
                <div class="caption captionfix">
                  <h3>Spiegel</h3>
                  <p>Huidige bod: <strong>€270.-</strong></p>
                  <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
              <div class="thumbnail">
                <div class="thumb_image" style="background-image:url(images/bmw.jpg);"></div>
                <div class="caption captionfix">
                  <h3>Spiegel</h3>
                  <p>Huidige bod: <strong>€270.-</strong></p>
                  <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
              <div class="thumbnail">
                <div class="thumb_image" style="background-image:url(images/eten.jpg);"></div>
                <div class="caption captionfix">
                  <h3>Spiegel</h3>
                  <p>Huidige bod: <strong>€270.-</strong></p>
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
