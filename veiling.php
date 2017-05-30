<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

$resultVoorwerp = null;
$resultImages = null;
$rubriek = -1;
$rootRubriek = -1;


if (isset($_GET['voorwerpnummer'])) {
  $voorwerpnummer = htmlspecialchars($_GET['voorwerpnummer']);

  $data = $db->prepare("SELECT
v.voorwerpnummer,
  v.titel,
  v.beschrijving,
  v.startprijs,
  v.betalingswijze,
  v.betalingsinstructie,
  v.postcode,
  v.plaatsnaam,
  v.land,
  v.looptijd,
  v.looptijdbegin,
  v.verzendkosten,
  v.verzendinstructie,
  v.verkoper,
  v.koper,
  v.looptijdeinde,
  v.veilinggesloten,
  v.verkoopprijs,
  vir.rubrieknummer as rn,
  l.lnd_Landnaam as landNaam,
  dbo.fnGetMinBid(v.voorwerpnummer) AS minimaalBod,
  dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
  FROM Voorwerp v
  JOIN VoorwerpInRubriek vir
    ON vir.voorwerpnummer = v.voorwerpnummer
  JOIN Landen l
    ON l.lnd_Code = v.land
    WHERE v.voorwerpnummer=?");
  $data->execute([$voorwerpnummer]);

  $resultVoorwerplist=$data->fetchAll();

  if(count($resultVoorwerplist) === 0){
    header("Location: index.php"); // voorwerpnummer ongeldig
  }
  else {
    $resultVoorwerp = $resultVoorwerplist[0];

    $data2 = $db->prepare("SELECT TOP 3 v.voorwerpnummer, titel, looptijdeinde, Foto.bestandsnaam
                           FROM Voorwerp v
                           CROSS APPLY
                           (
                               SELECT  TOP 1 Bestand.bestandsnaam
                               FROM    Bestand
                               WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
                           ) Foto
                           WHERE verkoper = ?
                           AND v.voorwerpnummer != ?
                           AND v.looptijdeinde > GETDATE()
                           ");
    $data2->execute(array($resultVoorwerp['verkoper'],$resultVoorwerp['voorwerpnummer']));
    $meerVanVerkoper = $data2->fetchAll();

    $rubriek = $resultVoorwerp['rn'];

    $data = $db->prepare("
SELECT TOP 4 bestandsnaam FROM Bestand b WHERE b.voorwerpnummer = ? ");
    $data->execute([$voorwerpnummer]);

    $resultImages=$data->fetchAll();

  }

}
else {
    // Geen voorwerpnummer opgegeven, redirect index.php
    header("Location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isUserLoggedIn($db)){
      $biedQuery = $db->prepare("INSERT INTO Bod(voorwerpnummer,bodbedrag,gebruiker,boddagtijd) VALUES(?,?,?,GETDATE())");
      $biedQuery->execute(array($resultVoorwerp['voorwerpnummer'],$_POST['price'],getLoggedInUser($db)['gebruikersnaam']));
    }
}
$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars($rubriek)));
$breadCrumb = $breadCrumbQuery->fetchAll();

$bidHistoryQuery = $db->prepare("SELECT b.voorwerpnummer,b.bodbedrag,b.boddagtijd,g.gebruikersnaam,g.bestandsnaam FROM Bod b
	JOIN
		Gebruikers g
			ON g.gebruikersnaam = b.gebruiker
WHERE voorwerpnummer = ? ORDER BY boddagtijd ASC");
$bidHistoryQuery->execute(array($resultVoorwerp['voorwerpnummer']));
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title><?php echo ($resultVoorwerp != null) ? $resultVoorwerp['titel'] : 'Veiling'; ?> - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
        <link href="css/veiling.css" rel="stylesheet">

  </head>
  <body>

    <?php include 'php/includes/header.php' ?>

<div class="container">
  <div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12">
      <div class="container-fluid content_col" style="padding-left:0px;">
        <?php
        if(count($breadCrumb) != 0)
        {
        ?>
          <div class="row navigation-row">
              <p>
                <a class="btn btn-default" href="javascript:history.go(-1)">
                <span class="glyphicon glyphicon-arrow-left"></span>  Overzicht
                </a>
                <a href="index.php" style="padding-left:20px;">
                  <span class="glyphicon glyphicon-home "></span>
                </a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="rubriek.php">Rubrieken</a>
                <?php
                    foreach($breadCrumb as $row)
                    {
                      if($row['rubrieknummer'] != $rootRubriek)
                      {
                      ?>
                        <span class="glyphicon glyphicon-menu-right"></span>
                        <a href="rubriek.php?rubriek=<?php echo $row['rubrieknummer']; ?>"><?php echo $row['rubrieknaam']; ?></a>
                      <?php
                      }
                    }
                ?>
              </p>
          </div>
        <?php
        }
        ?>
        <div class="row content_top_offset">
          <div class="col-lg-12 timer_row" >
            <div class="veiling-countdown">
              <h1 id="productCountDown" class="orange">&nbsp;</h1>
            </div>
            <div class="veiling-titel">
              <h2><?php echo ($resultVoorwerp != null) ? $resultVoorwerp['titel'] : ''; ?></h2>
            </div>
          </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#veiling" aria-controls="veiling" role="tab" data-toggle="tab">Veiling</a></li>
              <li role="presentation"><a href="#bieden" aria-controls="bieden" role="tab" data-toggle="tab">Biedgeschiedenis</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="veiling">

                            <div class="col-lg-4 left_content_row content_top_offset">
                                <div class="row thumb-image">
                                  <div class="carousel slide article-slide" id="article-photo-carousel">

                                      <!-- Wrapper for slides -->
                                      <div class="carousel-inner cont-slider">

                                        <?php
                                          $first = true;
                                          foreach($resultImages as $row){
                                            ?>
                                            <div class="item <?php echo ($first) ? 'active' : '' ;?>">
                                              <img alt="" title="" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                            </div>
                                            <?php
                                            $first = false;
                                          }
                                        ?>
                                      </div>
                                      <!-- Indicators -->
                                      <ol class="carousel-indicators">

                                        <?php
                                          $first = true;
                                          $index = 0;
                                          foreach($resultImages as $row){
                                            ?>
                                            <li class="<?php echo ($first) ? 'active' : '' ;?>" data-slide-to="<?php echo $index; ?>" data-target="#article-photo-carousel">
                                              <img alt="" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
                                            </li>
                                            <?php
                                            $first = false;
                                            $index++;
                                          }
                                        ?>
                                      </ol>
                                    </div>
                                    <br>
                                    <p>Land: <b><?php echo (!empty($resultVoorwerp['landNaam']))?$resultVoorwerp['landNaam']:"Onbekend";?></b></p>
                                    <p>Plaatsnaam: <b><?php echo (!empty($resultVoorwerp['plaatsnaam']))?$resultVoorwerp['plaatsnaam']:"Onbekend";?></b></p>
                                    <p>Postcode: <b><?php echo (!empty($resultVoorwerp['postcode']))?$resultVoorwerp['postcode']:"Onbekend";?></b></p>
                                    <br>
                                    <?php if(isset($resultVoorwerp['betalingswijze']) && !empty($resultVoorwerp['betalingswijze'])){ ?>
                                      <p>Betalingswijze: <b><?php echo $resultVoorwerp['betalingswijze']?></b></p>
                                    <?php }?>
                                    <?php if(isset($resultVoorwerp['betalingsinstructie']) && !empty($resultVoorwerp['betalingsinstructie'])){ ?>
                                      <p>Betalingsinstructie: <b><?php echo $resultVoorwerp['betalingsinstructie']?></b> </p>
                                    <?php }?>
                                    <br>
                                    <?php if(isset($resultVoorwerp['verzendkosten']) && !empty($resultVoorwerp['verzendkosten'])){ ?>
                                      <p>Verzendkosten: <b><?php echo $resultVoorwerp['verzendkosten']?></b></p>
                                    <?php }?>
                                    <?php if(isset($resultVoorwerp['verzendinstructie']) && !empty($resultVoorwerp['verzendinstructie'])){ ?>
                                      <p>Verzendinstructie: <b><?php echo $resultVoorwerp['verzendinstructie']?></b></p>
                                    <?php }?>
                                </div>
                            </div>
                            <div class="col-lg-8" style="margin-left:40px; margin-top:20px; width:60%">

                              <div style="padding: 5px;border: 1px solid;border-radius: 5px; border-color: #5484a4">
                                <?php
                                if($resultVoorwerp != null){
                                    $allowedTags = '<br><p><h1><h2><h3><h4><h5><h6><ul><li><ol><span><b><i><strong><small><mark><em><ins><sub><sup><del>';

                                      $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $resultVoorwerp['beschrijving']);
                                      $text = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $text);

                                      $stripped_text = strip_tags($text,$allowedTags);

                                      if(strlen($stripped_text) > 0)
                                        echo $stripped_text;
                                      else {
                                        echo 'Deze veiling heeft geen beschrijving';
                                      }

                                }
                                 ?>
                              </div>
                              <div class="text-left" style="margin-top:15px;border-top:1px solid #E6E6E6;">
                                <br>
                                <p>Verkoper:     <b><?php echo ($resultVoorwerp != null) ? $resultVoorwerp['verkoper'] : ''; ?></b><p>
                                <p>Startbedrag:  <b>€<?php echo ($resultVoorwerp != null) ? $resultVoorwerp['startprijs'] : ''; ?></b></p>
                                <p>Hoogste bod:  <b><?php echo ($resultVoorwerp['hoogsteBod'] != null) ? '&euro;'.$resultVoorwerp['hoogsteBod'] : 'Er is nog niet geboden';?></b></p>
                              </div>
                              <?php if(isUserLoggedIn($db)){
                                ?>
                                <form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                                  <div class="input-group" >
                                     <span class="input-group-addon">&euro;</span>
                                     <input style="height:36px;"class="form-control" type="number" required name="price" min="<?php echo $resultVoorwerp['minimaalBod']; ?>" value="<?php echo $resultVoorwerp['minimaalBod']; ?>" step="any">
                                     <span class="input-group-btn">
                                       <button class="btn btn-orange" type="submit"><img src="images/hamerwit.png" class="auction-hammer"></button>
                                     </span>
                                   </div>
                                 </form>
                                <?php
                              }else {
                                ?>
                                <p style="font-size:18px;margin-top:20px;">Om te bieden op deze veiling moet je ingelogd zijn, doe dat <a href="login.php">hier</a>.</p>
                                <?php
                              }?>
                            </div>
              </div>
              <div role="tabpanel" class="tab-pane" id="bieden">
                <div class="col-lg-12">
                  <div class="panel-body">
                         <ul class="chat">
                            <?php

                              $loggedInUser = '';

                              if(isUserLoggedIn($db))
                                $loggedInUser = getLoggedInUser($db)['gebruikersnaam'];

                              //echo $loggedInUser;

                              foreach($bidHistoryQuery->fetchAll() as $row){

                                if($row['gebruikersnaam'] == $loggedInUser){
                                ?>
                                <li class="right clearfix"><span class="chat-img pull-right">
                                  <?php
                                  if(file_exists('images/users/'.$row['bestandsnaam'])) {
                                    ?>
                                    <img width="50" height="50" style="background-image:url(images/users/<?php echo $row['bestandsnaam']; ?>);background-size:contain;" class="img-circle" />
                                    <?php
                                  }else {
                                    ?>
                                    <img width="50" height="50" style="background-image:url(images/users/geenfoto/geenfoto.png);background-size:contain;" class="img-circle" />
                                    <?php
                                  }?>
                                </span>
                                    <div class="chat-body clearfix">
                                        <div class="header">
                                            <small class=" text-muted"><span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                                            <strong class="pull-right primary-font"><?php echo $row['gebruikersnaam']; ?></strong>
                                        </div>
                                        <p style="float:right;">
                                             &euro;<?php echo $row['bodbedrag']; ?> geboden
                                        </p>
                                    </div>
                                </li>
                                <?php
                              }else {
                                ?>
                                <li class="left clearfix"><span class="chat-img pull-left">
                                  <?php
                                  if(file_exists('images/users/'.$row['bestandsnaam'])) {
                                    ?>
                                    <img width="50" height="50" style="background-image:url(images/users/<?php echo $row['bestandsnaam']; ?>);background-size:contain;" class="img-circle" />
                                    <?php
                                  }else {
                                    ?>
                                    <img width="50" height="50" style="background-image:url(images/users/geenfoto/geenfoto.png);background-size:contain;" class="img-circle" />
                                    <?php
                                  }?>
                                 </span>
                                     <div class="chat-body clearfix">
                                         <div class="header">
                                             <strong class="primary-font"><?php echo $row['gebruikersnaam']; ?></strong> <small class="pull-right text-muted">
                                                 <span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                                         </div>
                                         <p>
                                            &euro;<?php echo $row['bodbedrag']; ?> geboden
                                         </p>
                                     </div>
                                 </li>
                                <?php
                              }
                            }
                            ?>
                         </ul>
                     </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

  <br>
  <?php
    if(!empty($meerVanVerkoper)) {
   ?>
  <h2>
    Meer van deze verkoper
  </h2>
  <?php
  foreach($meerVanVerkoper as $row)
    {?>
    <div class="col-lg-4">
      <div>
        <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
          <h4 style="word-wrap: break-word;overflow:hidden;width:100%;height:19px;"> <?php echo $row['titel']; ?> </h4>
        </a>
      </div>
      <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
        <div class="veilingthumb" style="background-image:url('<?php echo $row['bestandsnaam']; ?>');">
          <p>Resterende tijd: <span id="rest_time_<?php echo $row['voorwerpnummer']; ?>">&nbsp;</span></p>
        </div>
      </a>
    </div>
    <script>
    var countDownDate = new Date('<?php echo $row['looptijdeinde']; ?>').getTime();

    var x = setInterval(function() {

      var now = new Date().getTime();
      var distance = countDownDate - now;

      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      document.getElementById("rest_time_<?php echo $row['voorwerpnummer']; ?>").innerHTML = days + "d " + hours + "h "
      + minutes + "m " + seconds + "s ";

      if (distance < 0) {
        clearInterval(x);
        document.getElementById("rest_time_<?php echo $row['voorwerpnummer']; ?>").innerHTML = "Gesloten";
      }
    }, 1000);
    </script>
    <?php }} ?>
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
  // Stop carousel
    $('.carousel').carousel({
      interval: false
    });
  </script>
  <script>
  var countDownDate = new Date('<?php echo ($resultVoorwerp != null) ? $resultVoorwerp['looptijdeinde'] : ''; ?>').getTime();

  var x = setInterval(function() {

    var now = new Date().getTime();
    var distance = countDownDate - now;

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("productCountDown").innerHTML = days + "d " + hours + "h "
    + minutes + "m " + seconds + "s ";

    if (distance < 0) {
      clearInterval(x);
      document.getElementById("productCountDown").innerHTML = "Gesloten";
    }
  }, 1000);
  </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
