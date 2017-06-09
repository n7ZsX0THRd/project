<?PHP
/*
  iProject Groep 2
  30-05-2017

  file: veiling.php
  purpose:
  Show auctions with details, with an option to bid
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// Include database, user functions
// Connect to database

//Create variables for rootRubriek,rubriek and
// resultVoorwerp
// With resultImages
// resulted by GET parameter voorwerpnummer
$resultVoorwerp = null;
$resultImages = null;
$rubriek = -1;
$rootRubriek = -1;



if (isset($_GET['voorwerpnummer']) && is_numeric($_GET['voorwerpnummer'])) {
  $voorwerpnummer = htmlspecialchars($_GET['voorwerpnummer']);
  //echo $voorwerpnummer;

  //Query, select all information from auctions
  // where voorwerpnummer = $GET voorwerpnummer
  $data = $db->prepare("SELECT TOP 1
  v.voorwerpnummer,
  v.titel,
  v.beschrijving,
  v.startprijs,
  bw.betalingswijze AS betalingswijze,
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
  LEFT JOIN VoorwerpInRubriek vir
    ON vir.voorwerpnummer = v.voorwerpnummer
  JOIN Betalingswijzen bw
	ON bw.ID = v.betalingswijze
  LEFT JOIN Landen l
    ON l.lnd_Code = v.land
    WHERE v.voorwerpnummer=?");
  $data->execute([$voorwerpnummer]);

  $resultVoorwerplist=$data->fetchAll();

  // If auction not found redirect to homepage
  if(count($resultVoorwerplist) === 0){
    header("Location: index.php"); // voorwerpnummer ongeldig
  }
  else {
    // resultVoorwerp = first record from resultList
    $resultVoorwerp = $resultVoorwerplist[0];

    // Select 3 sales from same seller
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

    // Select 3 sales that are recommended
    $data3 = $db->prepare("SELECT TOP 3 v.voorwerpnummer, titel, looptijdeinde, Foto.bestandsnaam
                           FROM Voorwerp v
                           CROSS APPLY
                           (
                               SELECT  TOP 1 Bestand.bestandsnaam
                               FROM    Bestand
                               WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
                           ) Foto
                           WHERE voorwerpnummer IN (
                                                   SELECT voorwerpnummer
                                                   FROM VoorwerpInRubriek
                                                   WHERE rubrieknummer IN (
                                                                          SELECT rubrieknummer
                                                                          FROM VoorwerpInRubriek
                                                                          WHERE voorwerpnummer = ?
                                                                          )
                                                   )
                           AND verkoper != ?
                           AND v.voorwerpnummer != ?
                           AND v.looptijdeinde > GETDATE()
                           ");
    $data3->execute(array($resultVoorwerp['voorwerpnummer'],$resultVoorwerp['verkoper'],$resultVoorwerp['voorwerpnummer']));
    $aanbevolen = $data3->fetchAll();
    // fill variable $meerVanVerkoper with data from database
    $rubriek = $resultVoorwerp['rn'];

    //Query select 4 images for auction
    $data = $db->prepare("SELECT TOP 4 bestandsnaam FROM Bestand b WHERE b.voorwerpnummer = ? ");
    $data->execute([$voorwerpnummer]);

    $resultImages=$data->fetchAll();

  }

}
else {
    // Geen voorwerpnummer opgegeven, redirect index.php
    header("Location: index.php");
}

//Requested post method
if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    // User trying to bid on auction
    if(isUserLoggedIn($db)){
      // check if user is logged in

      if(getLoggedInUser($db)['statusID'] == 1){
        // if user is blocked, set session for notifcation
        // refresh page
        $_SESSION['warning']['inactief_account'] = true;
        header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']); // USER IS INACTIEF
        exit();
      }
      else {

        // check if price isset and not empty
        if(isset($_POST['price']) && !empty($_POST['price']))
        {
          // Check if given price is numeric
          if(is_numeric($_POST['price'])){

            $highestBiedQuery = $db->prepare("SELECT dbo.fnGetMinBid(?) AS highestBid;");
            $highestBiedQuery->execute(array($resultVoorwerp['voorwerpnummer']));

            $resultHighest = $highestBiedQuery->fetchAll();

            // Check if query resulted
            if(count($resultHighest) > 0)
            {
              // Check if current offer is higher than highestbid
              if(((float)$_POST['price']) >= ((float)$resultHighest[0]['highestBid']))
              {
                if(((float)$_POST['price']) <= 999999999.99)
                {
                  //SELECT gebruiker FROM Bod WHERE voorwerpnummer = 110353566179 ORDER BY bodbedrag DESC
                  $laatsteBiederQuery = $db->prepare("SELECT gebruiker FROM Bod WHERE voorwerpnummer = ? ORDER BY bodbedrag DESC");
                  $laatsteBiederQuery->execute(array($resultVoorwerp['voorwerpnummer']));

                  $laatsteBiederResult = $laatsteBiederQuery->fetchAll();
                  // Get username from highestBid
                  if((count($laatsteBiederResult) > 0 && $laatsteBiederResult[0]['gebruiker'] != getLoggedInUser($db)['gebruikersnaam']) || count($laatsteBiederResult) == 0)
                  {
                    //Check if username from highestBid is not the same as current user
                    if($resultVoorwerp['veilinggesloten'] == 0)
                    {
                      if($resultVoorwerp['verkoper'] !== getLoggedInUser($db)['gebruikersnaam'])
                      {
                        $biedQuery = $db->prepare("INSERT INTO Bod(voorwerpnummer,bodbedrag,gebruiker,boddagtijd) VALUES(?,?,?,GETDATE())");
                        $biedQuery->execute(array($resultVoorwerp['voorwerpnummer'],$_POST['price'],getLoggedInUser($db)['gebruikersnaam']));

                        // Set session for succesfull and refresh
                        $_SESSION['warning']['succesvol_geboden'] = true;
                        header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                        exit();
                      }
                      else {
                        $_SESSION['warning']['eigen_veiling'] = true;
                        header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                        exit();
                      }
                    }
                    else {
                      $_SESSION['warning']['voorwerp_gesloten'] = true;
                      header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                      exit();
                    }
                  }
                  else {

                    //Place bid, since nobody else as bid yet
                    $_SESSION['warning']['overbied_jezelf'] = true;
                    header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                    exit();
                  }

                }
                else {
                  $_SESSION['warning']['prijs_tehoog'] = true;
                  header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                  exit();
                }
              }
              else {
                // the given price isn't a valid numeric value
                // set session for notifcation and refresh page to update potential updates
                $_SESSION['warning']['prijs_telaag'] = true;
                header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
                exit();
              }
            }
          }
          else {
              // the given price isn't a valid numeric value
              // set session for notifcation and refresh page to update potential updates
              $_SESSION['warning']['geengetal_prijs'] = true;
              header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
              exit();
          }
        }
        else {
          // Price is empty,
          // set session for notifcation and refresh page to update potential updates
          $_SESSION['warning']['empty_prijs'] = true;
          header("Location: veiling.php?voorwerpnummer=".$resultVoorwerp['voorwerpnummer']);
          exit();
        }
      }


    }
}
$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars($rubriek)));
$breadCrumb = $breadCrumbQuery->fetchAll();
//Query for breadcrumb of current article
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
                <span class="glyphicon glyphicon-arrow-left"></span>  Terug
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
          <div class="col-lg-12 timer_row">
            <div class="veiling-titel">
              <h2><?php echo ($resultVoorwerp != null) ? $resultVoorwerp['titel'] : ''; ?></h2>
            </div>
            <div class="veiling-countdown">
              <h1 id="productCountDown" class="orange">&nbsp;</h1>
            </div>
          </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#veiling" aria-controls="veiling" role="tab" data-toggle="tab">Veiling</a></li>
              <li role="presentation"><a href="#bieden" id="bied_refresh" aria-controls="bieden" role="tab" data-toggle="tab">Biedgeschiedenis</a></li>
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
                                          //Foreach over images from current auction
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
                                          //Foreach over images from current auction
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
                                    <?php
                                    // if user is logged In show option to bid
                                    if(isUserLoggedIn($db)){

                                      // show notification if user tried to place an offer
                                      if(isset($_SESSION['warning']['empty_prijs']) && $_SESSION['warning']['empty_prijs'] === true)
                                      {
                                      ?>
                                        <p class="bg-danger notifcation-fix" style="margin-left:0px;margin-right:0px;">De prijs kan niet leeg zijn.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['geengetal_prijs']) && $_SESSION['warning']['geengetal_prijs'] === true)
                                      {
                                      ?>
                                        <p class="bg-danger notifcation-fix" style="margin-left:0px;margin-right:0px;">De opgegeven prijs is geen geldige prijs.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['prijs_telaag']) && $_SESSION['warning']['prijs_telaag'] === true)
                                      {
                                      ?>
                                        <p class="bg-danger notifcation-fix" style="margin-left:0px;margin-right:0px;">De opgegeven prijs is te laag.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['prijs_tehoog']) && $_SESSION['warning']['prijs_tehoog'] === true)
                                      {
                                      ?>
                                        <p class="bg-danger notifcation-fix" style="margin-left:0px;margin-right:0px;">De opgegeven prijs is te hoog. Je kunt niet meer bieden dan &euro;999999999,99</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['overbied_jezelf']) && $_SESSION['warning']['overbied_jezelf'] === true)
                                      {
                                      ?>
                                        <p class="bg-danger notifcation-fix" style="margin-left:0px;margin-right:0px;">Je kan jezelf niet overbieden.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['inactief_account']) && $_SESSION['warning']['inactief_account'] === true)
                                      {
                                      ?>
                                        <p class="bg-warning notifcation-fix" style="margin-left:0px;margin-right:0px;">Jouw account is nog niet geverifieerd doe dit eerst om te kunnen bieden.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['voorwerp_gesloten']) && $_SESSION['warning']['voorwerp_gesloten'] === true)
                                      {
                                      ?>
                                        <p class="bg-warning notifcation-fix" style="margin-left:0px;margin-right:0px;">Je kunt niet meer bieden op dit voorwerp, de veiling is gesloten.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }
                                      else if(isset($_SESSION['warning']['eigen_veiling']) && $_SESSION['warning']['eigen_veiling'] === true)
                                      {
                                      ?>
                                        <p class="bg-warning notifcation-fix" style="margin-left:0px;margin-right:0px;">Je kunt niet op je eigen veiling bieden.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }else if(isset($_SESSION['warning']['succesvol_geboden']) && $_SESSION['warning']['succesvol_geboden'] === true)
                                      {
                                      ?>
                                        <p class="bg-success notifcation-fix" style="margin-left:0px;margin-right:0px;">Je hebt succesvol geboden.</p>
                                      <?php
                                        $_SESSION['warning'] = null;
                                      }
                                      //if user is inactive, show message
                                      if(getLoggedInUser($db)['statusID'] == 1){
                                        ?>
                                        <p class="bg-warning notifcation-fix" style="margin-left:0px;margin-right:0px;">Jouw account is nog niet geverifieerd doe dit eerst om te kunnen bieden.</p>
                                        <?php
                                      }
                                      else {
                                        // show input field for offer
                                        ?>
                                        <form method="POST" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                                          <div class="input-group" >
                                             <span class="input-group-addon">&euro;</span>
                                             <input style="height:36px;"class="form-control" type="number" required name="price" min="<?php echo number_format($resultVoorwerp['minimaalBod'], 2, '.', ''); ?>" value="<?php echo number_format($resultVoorwerp['minimaalBod'], 2, '.', ''); ?>" step="any">
                                             <span class="input-group-btn">
                                               <button class="btn btn-orange" type="submit"><img src="images/hamerwit.png" class="auction-hammer"></button>
                                             </span>
                                           </div>
                                         </form>
                                         <br/>
                                        <?php
                                      }
                                    }else {
                                      // Show message to login if user isn't logged in
                                      ?>
                                      <p class="bg-warning notifcation-fix" style="margin-left:0px;margin-right:0px;">Om te bieden op deze veiling moet je ingelogd zijn, doe dat <a href="login.php">hier</a>.</p>
                                      <?php
                                    }?>
                                    <!--  SHOW SELLER INFO -->
                                    <br>
                                    <table style="width:100%">
                                      <tr class="border_bottom">
                                        <td>Verkoper:</td>
                                        <td><b><?php echo ($resultVoorwerp != null) ? $resultVoorwerp['verkoper'] : ''; ?></b></td>
                                      </tr>
                                      <tr class="border_bottom">
                                        <td>Startbedrag:</td>
                                        <td><b><?php echo ($resultVoorwerp != null) ? '&euro;'.number_format($resultVoorwerp['startprijs'], 2, ',', '.') : ''; ?></b></td>
                                      </tr>
                                      <tr class="border_bottom">
                                        <td>Hoogste bod:</td>
                                        <td><b><?php echo ($resultVoorwerp['hoogsteBod'] != null) ? '&euro;'.number_format($resultVoorwerp['hoogsteBod'], 2, ',', '.') : 'Er is nog niet geboden';?></b></td>
                                      </tr>
                                      <tr class="border_bottom">
                                        <td>Land:</td>
                                        <td><b><?php echo (!empty($resultVoorwerp['landNaam']))?$resultVoorwerp['landNaam']:"Onbekend";?></b></td>
                                      </tr>
                                      <tr class="border_bottom">
                                        <td>Plaatsnaam:</td>
                                        <td><b><?php echo (!empty($resultVoorwerp['plaatsnaam']))?$resultVoorwerp['plaatsnaam']:"Onbekend";?></b></td>
                                      </tr>
                                      <tr class="border_bottom">
                                        <td>Postcode:</td>
                                        <td><b><?php echo (!empty($resultVoorwerp['postcode']))?$resultVoorwerp['postcode']:"Onbekend";?></b></td>
                                      </tr>
                                      <?php if(isset($resultVoorwerp['betalingswijze']) && !empty($resultVoorwerp['betalingswijze'])){ ?>
                                      <tr class="border_bottom">
                                        <td>Betalingswijze:</td>
                                        <td><b><?php echo $resultVoorwerp['betalingswijze']?></b></td>
                                      </tr>
                                      <?php }?>
                                      <?php if(isset($resultVoorwerp['betalingsinstructie']) && !empty($resultVoorwerp['betalingsinstructie'])){ ?>
                                      <tr class="border_bottom">
                                        <td>Betalingsinstructie:</td>
                                        <td><b><?php echo $resultVoorwerp['betalingsinstructie']?></b></td>
                                      </tr>
                                      <?php }?>
                                      <?php if(isset($resultVoorwerp['verzendkosten']) && !empty($resultVoorwerp['verzendkosten'])){ ?>
                                        <tr class="border_bottom">
                                          <td>Verzendkosten:</td>
                                          <td><b><?php echo '&euro;'.number_format($resultVoorwerp['verzendkosten'], 2, ',', '.')?></b></td>
                                        </tr>
                                      <?php }?>
                                      <?php if(isset($resultVoorwerp['verzendinstructie']) && !empty($resultVoorwerp['verzendinstructie'])){ ?>
                                        <tr class="border_bottom">
                                          <td>Verzendinstructie:</td>
                                          <td><b><?php echo $resultVoorwerp['verzendinstructie']?></b></td>
                                        </tr>
                                      <?php }?>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-8" style="margin-left:40px; margin-top:20px; width:60%">

                              <div style="padding: 5px;border: 1px solid;border-radius: 5px; border-color: #5484a4">


                                <?php


                                if($resultVoorwerp != null){
                                      // Allowed HTML elements in description
                                      $allowedTags = '<br><p><h1><h2><h3><h4><h5><h6><ul><li><ol><span><b><i><strong><small><mark><em><ins><sub><sup><del>';

                                      // Replace script and style elements
                                      $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $resultVoorwerp['beschrijving']);
                                      $text = preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/is', "$1$3", $text);

                                      $stripped_text = strip_tags($text,$allowedTags);
                                      $stripped_text = str_replace ("<p><br></p>", "", $stripped_text);
                                      //Strip html elements
                                      //and replace <p><br></p> with nothing

                                      // Check if stripped text is longer > 0
                                      if(strlen($stripped_text) > 0)
                                        echo $stripped_text;
                                      else {
                                        echo 'Deze veiling heeft geen beschrijving'; // Show message
                                      }

                                }
                                 ?>
                              </div>
                            </div>
              </div>
              <div role="tabpanel" class="tab-pane" id="bieden">

                <div class="col-lg-12">
                  <div class="panel-body" id="bieden_content">
                        <!-- CONTENT FROM AJAX -->
                        <!-- Bid History Body, content get's loaded by ajax when user clicks BidHistory button the tabs-->
                </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  <?php
    // If seller has multiple auctions
    //Show them
    if(!empty($meerVanVerkoper)) {
   ?>
   <div class="row">
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


    var x = setInterval(function() {
    var countDownDate = new Date('<?php echo $row['looptijdeinde']; ?>').getTime();
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
    <?php }
    ?>
  </div>
    <?php
  } ?>
    <br>
    <?php
      // If seller has multiple auctions
      //Show them
      if(!empty($aanbevolen)) {
     ?>
     <div class="row">
    <h2>
      Aanbevolen veilingen
    </h2>
    <?php
    foreach($aanbevolen as $row)
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


      var x = setInterval(function() {
      var countDownDate = new Date('<?php echo $row['looptijdeinde']; ?>').getTime();
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

      <?php }
      ?>
        </div>
      <?php
    } ?>
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
  $('#bied_refresh').click(function(){
    $( "#bieden_content" ).load( "php/includes/bied_geschiedenis.php?voorwerpnummer=<?php echo $resultVoorwerp['voorwerpnummer'];?>" );
  });
  $(document).ready(function(){
    $( "#bieden_content" ).load( "php/includes/bied_geschiedenis.php?voorwerpnummer=<?php echo $resultVoorwerp['voorwerpnummer'];?>" );
  });

  </script>
</body>
</html>
<?php
//$_SESSION['warning'] = null;
?>
