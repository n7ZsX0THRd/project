<?php
/*
  iProject Groep 2
  02-06-2017

  file: veilingen.php
  purpose:
  Showing users auctions
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

// Get username
$username = getLoggedInUser($db)['gebruikersnaam'];

$dataquery= $db->prepare("SELECT	titel,
											MAX(bodbedrag) as bodbedragMAX,
											V.looptijdeinde,
											V.startprijs,
											bestandsnaam,
											V.voorwerpnummer,
											dbo.fnGetHoogsteBod(b.voorwerpnummer) AS hoogsteBod,
											COUNT(b.bodbedrag) AS aantalbiedingen
								FROM Voorwerp AS V
											LEFT JOIN Bod B
											ON b.voorwerpnummer = v.voorwerpnummer
												CROSS APPLY
													(
													SELECT  TOP 1 B.bestandsnaam
													FROM    Bestand AS B
													WHERE   B.voorwerpnummer = V.voorwerpnummer
													) Foto
								WHERE	V.verkoper = ?
												AND  v.veilinggesloten = 0
								GROUP BY	titel,
											B.voorwerpnummer,
											V.looptijdeinde,
											V.startprijs,
											bestandsnaam,
											V.voorwerpnummer,
											V.looptijdbegin
								ORDER BY V.looptijdbegin DESC");
$dataquery->execute(array($username));

//Query for getting al the users closed auctions
$dataqueryverlopen= $db->prepare("SELECT V.titel,
                                   V.voorwerpnummer,
                                   V.looptijdeinde,
                                   B.bodbedrag,
                                   dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod,
                                   foto.bestandsnaam,
                                   COUNT(b.bodbedrag) AS aantalbiedingen
                            FROM Voorwerp V
                            LEFT JOIN Bod B
                            ON b.voorwerpnummer = v.voorwerpnummer
                                CROSS APPLY
                                (
                                SELECT  TOP 1 Bestand.bestandsnaam
                                FROM    Bestand
                                WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
                                ) Foto
                            WHERE V.verkoper = ?
                            AND  v.veilinggesloten = 1
                            GROUP BY V.voorwerpnummer,
                                     V.titel,
                                     V.looptijdeinde,
                                     B.bodbedrag,
                                     dbo.fnGetHoogsteBod(v.voorwerpnummer),
                                     foto.bestandsnaam");
$dataqueryverlopen->execute(array($username));

//Getting the query results
$dataqueryresult = $dataquery->fetchAll();
$dataqueryverlopenresult = $dataqueryverlopen->fetchAll();

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

      <!-- Content -->
      <div class="col-md-9 col-lg-10 col-sm-8">
        <div class="container-fluid content_col">
          <div class="row navigation-row fix">
              <h1 style="margin-bottom: 10px" >Veilingen</h1>
              <p>
                <a href="index.php">
                  <span class="glyphicon glyphicon-home "></span>
                </a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="account.php">Mijn Account</a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="veilingen.php">Veilingen</a>
              </p>
          </div>

              <div class="row content_top_offset">
                <?php
                  // Notification when an auction is succesfully placed
                  if(isset($_GET['succes']) || isset($_GET['success']))
                  {
                    ?>
                      <p class="bg-success" style="padding:5px;">De veiling is succesvol geplaats.</p>
                    <?php
                  }
                ?>
                <div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Lopende veilingen (<?php echo count($dataqueryresult) ?>)</a></li>
                      <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Gesloten veilingen (<?php echo count($dataqueryverlopenresult) ?>)</a></li>
                    </ul>

                    <!-- HUIDIGE VEILINGEN -->
                    <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active" id="home">
                          <?php
                          // Load in all the auctions
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
                                <p>Aantal biedingen: <strong><?php echo $row['aantalbiedingen']?></strong></p>
                                <p>Startprijs: <strong>&euro;<?php echo $row['startprijs']?></strong></p>
                                <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', '.'): 'Er is nog niet geboden';?></strong></p>
                                <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                              </div>
                            </div>
                          </div>
                          <?php }
                          // If no auctions show notification
                          if($indexhuidig==0){
                              ?>
                              <p class="bg-warning" style="padding:5px;margin-top:15px;">
                                  Je hebt nog geen veilingen<br>
                              </p>
                              <?php
                          }
                          ?>
                        </div>
                      <!-- END ACTIVE AUCTIONS -->

                      <!-- NONACTIVE AUCTIONS -->
                      <div role="tabpanel" class="tab-pane" id="profile">
                        <?php
                        // Load in all the auctions
                        $indexverlopen=0;
                          foreach($dataqueryverlopenresult as $row){
                            $indexverlopen ++;
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
                                  <p>Uw Bod: <strong>&euro;<?php echo number_format($row['bodbedrag'], 2, ',', '.')?></strong></p>
                                  <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', '.'): 'Er is nog niet geboden';?></strong></p>
                                  <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bekijken</a></p>
                                </div>
                              </div>
                            </div>
                            <?php }
                            // if no actions show notifications
                            if($indexverlopen==0){
                                ?>
                                <p class="bg-warning" style="padding:5px;margin-top:15px;">
                                    Je hebt nog geen gesloten veilingen<br>
                                </p>
                                <?php
                            }
                            ?>
                          </div>
                          <!-- END NONACTIVE AUCTIONS  -->
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
