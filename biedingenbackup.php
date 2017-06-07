<?php
/*
  iProject Groep 2
  30-05-2017

  file: account.php
  purpose:
  Show shortcuts for user
*/
session_start();
// Start Session
// Include database connection and user functins
include_once('php/database.php');
include_once('php/user.php');
pdo_connect();
// Connect with database


// If user is not logged In redirect to homepage
if(isUserLoggedIn($db) == false)
  header("Location: index.php");



$_SESSION['menu']['sub'] = 'ma';
// Set session for sidebar menu,
// ma -> my account


$username = getLoggedInUser($db)['gebruikersnaam'];
$dataquery= $db->prepare("SELECT V.titel,
                                          v.voorwerpnummer,
                                          bodbedrag, boddagtijd,
                                          looptijdeinde,
                                          foto.bestandsnaam,
                                          dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
                          FROM Bod B
                          INNER JOIN voorwerp V
                          ON B.voorwerpnummer = V.voorwerpnummer
						                     CROSS APPLY
								                 (
								                 SELECT  TOP 1 Bestand.bestandsnaam
								                 FROM    Bestand
                                 WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
								                 ) Foto
                          WHERE gebruiker=?
                          AND bodbedrag = dbo.fnGetHoogsteBod(v.voorwerpnummer)
                          AND v.veilinggesloten = 0");
$dataquery->execute(array($username));

$dataqueryoverboden= $db->prepare("SELECT DISTINCT V.titel,
                                          v.voorwerpnummer,
                                          bodbedrag, boddagtijd,
                                          looptijdeinde,
                                          foto.bestandsnaam,
                                          dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
                          FROM Bod B
                          INNER JOIN voorwerp V
                          ON B.voorwerpnummer = V.voorwerpnummer
						                     CROSS APPLY
								                 (
								                 SELECT  TOP 1 Bestand.bestandsnaam
								                 FROM    Bestand
                                 WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
								                 ) Foto
                          WHERE gebruiker=?
                          AND dbo.fnGetHoogsteBod(v.voorwerpnummer) > bodbedrag
                          AND v.veilinggesloten = 0
                          ORDER BY bodbedrag DESC");
$dataqueryoverboden->execute(array($username));

$dataquerygewonnen= $db->prepare("SELECT DISTINCT V.titel,
                                          v.voorwerpnummer,
                                          bodbedrag, boddagtijd,
                                          looptijdeinde,
                                          foto.bestandsnaam,
                                          dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
                          FROM Bod B
                          INNER JOIN voorwerp V
                          ON B.voorwerpnummer = V.voorwerpnummer
						                     CROSS APPLY
								                 (
								                 SELECT  TOP 1 Bestand.bestandsnaam
								                 FROM    Bestand
                                 WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
								                 ) Foto
                          WHERE gebruiker=?
                          AND bodbedrag = dbo.fnGetHoogsteBod(v.voorwerpnummer)
                          AND v.veilinggesloten = 1");
$dataquerygewonnen->execute(array($username));

$dataqueryverloren= $db->prepare("SELECT DISTINCT V.titel,
                                          v.voorwerpnummer,
                                          bodbedrag, boddagtijd,
                                          looptijdeinde,
                                          foto.bestandsnaam,
                                          dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
                          FROM Bod B
                          INNER JOIN voorwerp V
                          ON B.voorwerpnummer = V.voorwerpnummer
						                     CROSS APPLY
								                 (
								                 SELECT  TOP 1 Bestand.bestandsnaam
								                 FROM    Bestand
                                 WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
								                 ) Foto
                          WHERE gebruiker=?
                          AND bodbedrag != dbo.fnGetHoogsteBod(v.voorwerpnummer)
                          AND v.veilinggesloten = 1");
$dataqueryverloren->execute(array($username));

$dataqueryresult = $dataquery->fetchAll();
$dataqueryoverbodenresult = $dataqueryoverboden->fetchAll();
$dataquerygewonnenresult = $dataquerygewonnen->fetchAll();
$dataqueryverlorenresult = $dataqueryverloren->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include 'php/includes/default_header.php'; ?>
      <link href="css/dashboard.css" rel="stylesheet">
      <title>Mijn biedingen</title>
  </head>

  <body>

    <?php
      include 'php/includes/header.php';
      // Include header
    ?>


    <div class="container">
      <div class="col-md-3 col-lg-2 col-sm-4 sidebar">
        <?php
          include 'php/includes/sidebar.php';
          // Include sidebar
        ?>
      </div>


      <div class="col-md-9 col-lg-10 col-sm-8">
        <div class="container-fluid content_col">
          <div class="row">
              <h1 style="margin-bottom: 4%" >Biedingen</h1>
              <div class="row navigation-row">
                  <p>
                    <a href="index.php">
                      <span class="glyphicon glyphicon-home "></span>
                    </a>
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <a href="account.php">Mijn Account</a>
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <a href="biedingen.php">Biedingen</a>
                  </p>
              </div>
              <div class="row item-row">
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Hoogste bod (<?php echo count($dataqueryresult) ?>)</a></li>
                      <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Overboden (<?php echo count($dataqueryoverbodenresult) ?>)</a></li>
                      <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Gewonnen (<?php echo count($dataquerygewonnenresult) ?>)</a></li>
                      <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Verloren (<?php echo count($dataqueryverlorenresult) ?>)</a></li>
                    </ul>

                    <!-- HUIDIGE BIEDINGEN -->
                    <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active" id="home">
                          <?php
                          $indexhuidig=0;
                          foreach($dataqueryresult as $row){
                            $indexhuidig ++;
                            ?>
                          <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12">
                            <div class="row item-thumb" style="display: flex;">
                              <div class="col-lg-3 col-xs-3 col-sm-4 col-md-4" >
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                                  <div class="item-row-image" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                  </div>
                                </a>
                              </div>
                              <div class="col-lg-9 col-xs-9 col-sm-8 col-md-8" style="position:relative;flex: 1;">
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>"><h3 class="item-row-titel"><?php echo $row['titel']?></h3></a>
                                <h3 style="font-size:14px;" class="orange" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>">&nbsp;</h3>
                                <p>Uw Bod: <strong>&euro;<?php echo number_format($row['bodbedrag'], 2, ',', '')?></strong></p>
                                <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', ''): 'Er is nog niet geboden';?></strong></p>
                                <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                              </div>
                            </div>
                          </div>
                          <?php }
                          if($indexhuidig==0){
                              echo '<br><p><strong style="font-size:18px; padding-left:15px;">U heeft nog niet geboden</strong></p>';
                          }
                          ?>
                        </div>

                      <!-- OVERBODEN BIEDINGEN -->
                      <div role="tabpanel" class="tab-pane" id="profile">
                        <?php
                        $indexoverboden=0;
                          foreach($dataqueryoverbodenresult as $row){
                            $indexoverboden ++;
                            ?>
                            <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12">
                              <div class="row item-thumb" style="display: flex;">
                                <div class="col-lg-3 col-xs-3 col-sm-4 col-md-4" >
                                  <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                                    <div class="item-row-image" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                    </div>
                                  </a>
                                </div>
                                <div class="col-lg-9 col-xs-9 col-sm-8 col-md-8" style="position:relative;flex: 1;">
                                  <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>"><h3 class="item-row-titel"><?php echo $row['titel']?></h3></a>
                                  <h3 style="font-size:14px;" class="orange" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>">&nbsp;</h3>
                                  <p>Uw Bod: <strong>&euro;<?php echo number_format($row['bodbedrag'], 2, ',', '')?></strong></p>
                                  <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', ''): 'Er is nog niet geboden';?></strong></p>
                                  <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                                </div>
                              </div>
                            </div>
                            <?php }
                            if($indexoverboden==0){
                                echo '<br><p><strong style="font-size:18px; padding-left:15px;">U bent nog niet overboden</strong></p>';
                            }
                            ?>
                          </div>

                      <!-- GEWONNEN BIEDINGEN -->
                      <div role="tabpanel" class="tab-pane" id="messages">
                        <?php
                        $indexgewonnen=0;
                        foreach($dataquerygewonnenresult as $row){
                          $indexgewonnen ++;
                          ?>
                          <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12">
                            <div class="row item-thumb" style="display: flex;">
                              <div class="col-lg-3 col-xs-3 col-sm-4 col-md-4" >
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                                  <div class="item-row-image" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                  </div>
                                </a>
                              </div>
                              <div class="col-lg-9 col-xs-9 col-sm-8 col-md-8" style="position:relative;flex: 1;">
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>"><h3 class="item-row-titel"><?php echo $row['titel']?></h3></a>
                                <h3 style="font-size:14px;" class="orange" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>">&nbsp;</h3>
                                <p>Uw Bod: <strong>&euro;<?php echo number_format($row['bodbedrag'], 2, ',', '')?></strong></p>
                                <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', ''): 'Er is nog niet geboden';?></strong></p>
                                <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                              </div>
                            </div>
                          </div>
                          <?php }
                          if($indexgewonnen==0){
                              echo '<br><p><strong style="font-size:18px; padding-left:15px;">U heeft nog niets gewonnen</strong></p>';
                          }
                          ?>
                          </div>

                      <!-- VERLOREN BIEDINGEN -->
                      <div role="tabpanel" class="tab-pane" id="settings">
                        <?php
                        $indexverloren=0;
                        foreach($dataqueryverlorenresult as $row){
                          $indexverloren ++;
                          ?>
                          <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12">
                            <div class="row item-thumb" style="display: flex;">
                              <div class="col-lg-3 col-xs-3 col-sm-4 col-md-4" >
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
                                  <div class="item-row-image" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                  </div>
                                </a>
                              </div>
                              <div class="col-lg-9 col-xs-9 col-sm-8 col-md-8" style="position:relative;flex: 1;">
                                <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>"><h3 class="item-row-titel"><?php echo $row['titel']?></h3></a>
                                <h3 style="font-size:14px;" class="orange" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>">&nbsp;</h3>
                                <p>Uw Bod: <strong>&euro;<?php echo number_format($row['bodbedrag'], 2, ',', '')?></strong></p>
                                <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', ''): 'Er is nog niet geboden';?></strong></p>
                                <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                              </div>
                            </div>
                          </div>
                          <?php }
                          if($indexverloren==0){
                              echo '<br><p><strong style="font-size:18px; padding-left:15px;">U heeft nog niets verloren</strong></p>';
                          } ?>
                      </div>
                    </div>

                  </div>





             </div>
          </div>


      </div>
      <!-- CONTAINER END -->
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

      $( "h3#looptijdeinde" ).each(function( index ) {
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
