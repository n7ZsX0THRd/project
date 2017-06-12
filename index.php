<?php
/*
  iProject Groep 2
  30-05-2017

  file: index.php
  purpose:
  Show list of soon ending auctions
  Show auctions with a lot bids
  Show new auctions
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// Include database, and include user functions.
// Connect to database

// If user logged in and requested new verification email
if(isUserLoggedIn($db))
{
  if(isset($_GET['mail'])==true) {
          // Check if email isset in get

          $random = rand(100000,999999);
          // Create new verifcationcode

          $code = update_verification_for_user(array('email' => $_SESSION['email'],'verificatiecode' => $random), $db);
          // Update verifcation for user
          if($code != 0) {

            $gebruikersnaam = getLoggedInUser($db)['gebruikersnaam'];
            // Get username from loggedIn user, for email

            $to = $_SESSION['email'];
            $subject = 'Nieuwe activatiecode voor EenmaalAndermaal';
            // Email body and subject
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
            // Use sendMail function from mail.php to send email
          }
      $nieuwe_mail = 1;
  }
}
// Select all child rubrieks from parent rubriek
$childrenRubriekenQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam FROM Rubriek WHERE parentRubriek = -1 ORDER BY volgnr ASC, rubrieknaam ASC");
$childrenRubriekenQuery->execute();
$childrenRubrieken = $childrenRubriekenQuery->fetchAll();


// Query to select top 3 soon ending auctions
$lastChanceQuery = $db->prepare("SELECT TOP 3 * FROM
(
	SELECT
		v.voorwerpnummer,
		v.looptijdeinde,
		v.titel,
		Foto.bestandsnaam,
		dbo.fnGetMinBid(v.voorwerpnummer) AS minimaalBod,
		dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
	FROM Voorwerp v
		CROSS APPLY
		(
			SELECT  TOP 1
				Bestand.bestandsnaam
			FROM    Bestand
			WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
		)
		Foto
	WHERE DATEADD(MI,30,GETDATE()) < v.looptijdeinde
) AS s
ORDER BY looptijdeinde ASC");
$lastChanceQuery->execute();
// Execute query

// Query to select new auctions
$newItemsQuery = $db->prepare("SELECT TOP 4
        v.voorwerpnummer,
        v.titel,
        v.looptijdeinde,
        Foto.bestandsnaam,
        dbo.fnGetMinBid(v.voorwerpnummer) AS minimaalBod,
        dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
      FROM Voorwerp v CROSS APPLY
        (
        SELECT  TOP 1 Bestand.bestandsnaam
        FROM    Bestand
        WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
        AND     v.veilinggesloten = 0
        ) Foto ORDER BY v.looptijdbegin DESC");
$newItemsQuery->execute();
// Execute query

// Query to select populair auctions
$populairItemsQuery = $db->prepare("SELECT TOP 4
	v.voorwerpnummer,
	v.titel,
	v.looptijdeinde,
  Foto.bestandsnaam,
  dbo.fnGetMinBid(v.voorwerpnummer) AS minimaalBod,
  dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod,
	COUNT(*) AS boden
FROM Voorwerp v
  CROSS APPLY
  (
    SELECT  TOP 1 Bestand.bestandsnaam
    FROM    Bestand
    WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
  ) Foto
	INNER JOIN Bod b
		ON b.voorwerpnummer = v.voorwerpnummer
WHERE DATEADD(MI,30,GETDATE()) < v.looptijdeinde
	GROUP BY
		v.voorwerpnummer,
		v.titel,
		v.looptijdeinde,
    Foto.bestandsnaam
ORDER BY
	boden DESC ,
	v.looptijdeinde ASC");
$populairItemsQuery->execute();
// Execute query
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php
          include 'php/includes/default_header.php';
          // Include default head
        ?>

        <title>Veilingsite - Eenmaal Andermaal</title>
  </head>

  <body>

    <?php
      include 'php/includes/header.php'
      // Include navigation
    ?>

  <div id="myCarousel" class="carousel slide" data-ride="carousel" style="margin-top:-20px;">
  <!-- Indicators -->
    <ol class="carousel-indicators" style="display:none">
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      <li data-target="#myCarousel" data-slide-to="1"></li>

    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
    <div class="item active banner">
        <center class="bannercontent">
          Veil nu snel al uw oude spullen, en vang er nog een leuk bedrag voor!
        </center>

    </div>
      <div class="item">
        <div class="container">
          <div class="row veilingitem">
            <div class="col-lg-12">
              <h4 class="carousel-header">Laatste kans</h4>
            </div>
            <?php
            // Loop over soon ending auctions and show them in slider
            foreach($lastChanceQuery->fetchAll() as $row)
            {
                ?>
                <div class="col-lg-4">
                  <div>
                    <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" alt="<?php echo $row['titel']; ?>">
                      <h4 style="word-wrap: break-word;overflow:hidden;width:100%;height:19px;"> <?php echo $row['titel']; ?> </h4>
                    </a>
                  </div>
                  <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" alt="<?php echo $row['titel']; ?>">
                    <div class="veilingthumb" style="background-image:url('<?php echo $row['bestandsnaam']; ?>');">
                      <p>Resterende tijd: <span id="countdown" data-looptijd="<?php echo $row['looptijdeinde'];?>"> &nbsp;<?php //echo $row['looptijdeinde']; ?></span></p>
                    </div>
                  </a>
                </div>
                <?php
            }

            ?>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- END SLIDER -->
    <?php
    // Check if user is LoggedIn and check if account is verified.
    // If not veriefied, show option to send another email
    if (isUserLoggedIn($db)){
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
                        if(isset($nieuwe_mail) && $nieuwe_mail == 1) {
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
            // If user is loggedIn show user sidebar, otherwise show all child rubrieks from the rootrubriek
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
                      // Loop over child rubrieks from root rubriek
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
                    <h4 class="carousel-header">Nu populair</h4>
                    <?php

                    // Loop over new auctions
                    foreach($populairItemsQuery->fetchAll() as $row){
                      ?>
                      <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                         <div class="thumbnail">
                          <a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                            <h3 style="padding-left: 10px;word-wrap: break-word;overflow: hidden;height:24px;"><?php echo $row['titel']; ?></h3>
                          </a>
                          <a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                            <div class="thumb_image" style="background-image:url(<?php echo $row['bestandsnaam']; ?>);"></div>
                          </a>
                          <div class="caption captionfix">
                            <h3><span id="countdown" data-looptijd="<?php echo $row['looptijdeinde'];?>">&nbsp;</span></h3>
                            <?php
                            if($row['hoogsteBod'] != null){
                              ?>
                                <p>Hoogste bod: <br><strong><?php echo '&euro;'.number_format($row['hoogsteBod'], 2, ',', '.')?></strong></p>
                              <?php
                            }
                            else
                            {
                              ?>
                                <p>Start prijs: <br><strong><?php echo '&euro;'.number_format($row['minimaalBod'], 2, ',', '.')?></strong></p>
                              <?php
                            }
                            ?>

                            <p><a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                          </div>
                         </div>
                      </div>
                      <?php
                    }
                    ?>
                  </div>
                </div>

                <div class="row item-row">
                  <h4 class="carousel-header">Nieuwe veilingen</h4>
                  <?php

                  // Loop over new auctions
                  foreach($newItemsQuery->fetchAll() as $row){
                    ?>
                    <div class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                       <div class="thumbnail">
                        <a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                          <h3 style="padding-left: 10px;word-wrap: break-word;overflow: hidden;height:24px;"><?php echo $row['titel']; ?></h3>
                        </a>
                        <a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                          <div class="thumb_image" style="background-image:url(<?php echo $row['bestandsnaam']; ?>);"></div>
                        </a>
                        <div class="caption captionfix">
                          <h3><span id="countdown" data-looptijd="<?php echo $row['looptijdeinde'];?>">&nbsp;</span></h3>
                          <?php
                          if($row['hoogsteBod'] != null){
                            ?>
                              <p>Hoogste bod: <br><strong><?php echo '&euro;'.number_format($row['hoogsteBod'], 2, ',', '.')?></strong></p>
                            <?php
                          }
                          else
                          {
                            ?>
                              <p>Start prijs: <br><strong><?php echo '&euro;'.number_format($row['minimaalBod'], 2, ',', '.')?></strong></p>
                            <?php
                          }
                          ?>

                          <p><a alt="<?php echo $row['titel']; ?>" href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bieden</a></p>
                        </div>
                       </div>
                    </div>
                    <script>
                    // Function to update the count downs of the auctions
                    var countDownDate = new Date('<?php echo $row['looptijdeinde']; ?>').getTime();

                    var x = setInterval(function() {

                      var now = new Date().getTime();
                      var distance = countDownDate - now;

                      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                      document.getElementById("count_<?php echo $row['voorwerpnummer']; ?>").innerHTML = days + "d " + hours + "h "
                      + minutes + "m " + seconds + "s ";

                      if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("count_<?php echo $row['voorwerpnummer']; ?>").innerHTML = "Gesloten";
                      }
                    }, 1000);
                    </script>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      </div>
    </div>
    </div>

    <?php
    include 'php/includes/footer.php';
    // Include footer
    ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>
    var x = setInterval(function() {

      $( "span#countdown" ).each(function( index ) {
          var countDownDate = new Date($( this ).data("looptijd")).getTime();
          var now = new Date().getTime();
          var distance = countDownDate - now;

          var days = Math.floor(distance / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          $( this ).text(days + "d " + hours + "h "
          + minutes + "m " + seconds + "s ");

          if (distance < 0) {
            $( this ).text("Gesloten");
          }
      });

    }, 1000);

    </script>
  </body>
</html>
