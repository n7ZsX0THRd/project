<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$rootRubriek = -1;
$rubriekID = $rootRubriek;

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
  if(isset($_GET['rubriek']) && !empty($_GET['rubriek']))
  {
      $dbs = $db->prepare("SELECT TOP(1) rubrieknummer FROM Rubriek WHERE rubrieknummer = ?");
      $dbs->execute(array(htmlspecialchars($_GET['rubriek'])));
      $resultCount = count($dbs->fetchAll());

      if($resultCount == 1)
        $rubriekID = $_GET['rubriek'];
  }
}

$rubriekQuery = $db->prepare("SELECT TOP(1) rubrieknummer,rubrieknaam,parentRubriek,volgnr FROM Rubriek WHERE rubrieknummer = ?");
$rubriekQuery->execute(array(htmlspecialchars($rubriekID)));
$rubriek = $rubriekQuery->fetchAll()[0];


$childrenRubriekenQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam FROM Rubriek WHERE parentRubriek = ? ORDER BY volgnr ASC, rubrieknaam ASC");
$childrenRubriekenQuery->execute(array(htmlspecialchars($rubriekID)));
$childrenRubrieken = $childrenRubriekenQuery->fetchAll();

$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars($rubriekID)));
$breadCrumb = $breadCrumbQuery->fetchAll();


$voorwerpenQuery = $db->prepare("SET STATISTICS TIME ON


SELECT
v.voorwerpnummer,
v.titel,
v.startprijs,
vir.rubrieknummer,
r.parentRubriek

FROM Voorwerp v
	JOIN
		VoorwerpInRubriek vir
			ON v.voorwerpnummer = vir.voorwerpnummer
	JOIN
		Rubriek r
			ON r.rubrieknummer = vir.rubrieknummer
	WHERE EXISTS
	(
	 SELECT * FROM
	 Rubriek
     WHERE dbo.fnRubriekIsAfstammelingVan(rubrieknummer,?) = 1
	   AND vir.rubrieknummer = rubriek.rubrieknummer
	)
ORDER BY Titel
OFFSET 30*? ROWS
FETCH NEXT 30 ROWS ONLY


SET STATISTICS TIME OFF");

$voorwerpenQuery->execute(array($rubriek['rubrieknummer'],$_GET['page'])); // RUBRIEK ID, START NUMBER, END NUMBER

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Profiel - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
  </head>
  <body>

    <?php include 'php/includes/header.php' ?>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-lg-2 col-sm-4 sidebar">
      <h3></h3>


      <ul class="menubar">
        <?php
        if($rubriek['rubrieknummer'] != -1){

            if(count($childrenRubrieken) == 0)
            {
              $parentQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam, parentRubriek FROM Rubriek WHERE rubrieknummer = ? ORDER BY volgnr ASC, rubrieknaam ASC");
              $parentQuery->execute(array(htmlspecialchars($rubriek['parentRubriek'])));
              $parent = $parentQuery->fetchAll();
              if(count($parent) != 0)
              {
              ?>
                <li class="toggle-sub active">
                  <a href="rubriek.php?rubriek=<?php echo $parent[0]['parentRubriek']; ?>"><?php echo $parent[0]['rubrieknaam']; ?></a>
                </li>
              <?php
              }
            }
            else {
              ?>
              <li class="toggle-sub active">
                <a href="rubriek.php?rubriek=<?php echo $rubriek['parentRubriek']; ?>"><?php echo $rubriek['rubrieknaam']; ?></a>
              </li>
              <?php
            }
        }else{
          ?>
          <li class="toggle-sub active">
            <a href="">Rubrieken</a>
          </li>
          <?php
        }?>
        <ul class="sub">
          <?php

            if(count($childrenRubrieken) == 0)
            {

              $childrenRubriekenQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam FROM Rubriek WHERE parentRubriek = ? ORDER BY volgnr ASC, rubrieknaam ASC");
              $childrenRubriekenQuery->execute(array(htmlspecialchars($rubriek['parentRubriek'])));
              $childrenRubrieken = $childrenRubriekenQuery->fetchAll();

            }


            foreach($childrenRubrieken as $row)
            {
              ?>
              <li>
                <a class="<?php echo ($row['rubrieknummer'] == $rubriek['rubrieknummer']) ? 'active-menu-item' : 'nonactive-menu-item'; ?>" href="rubriek.php?rubriek=<?php echo $row['rubrieknummer']; ?>"><?php echo $row['rubrieknaam']; ?></a>
              </li>
              <?php
            }
          ?>

        </ul>
      </ul>
    </div>
    <div class="col-md-8 col-lg-10 col-sm-8">
      <div class="container-fluid  content_col">
        <?php
        if(count($breadCrumb) != 0)
        {
        ?>
          <div class="row navigation-row">
              <p>
                <a href="index.php">
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
          <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12"  style="border-bottom:2px solid #E6E6E6;">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Zoek naar een veiling..." aria-describedby="sizing-addon2">
              <span class="input-group-addon" id="sizing-addon2"><span class="glyphicon glyphicon-search"></span></span>
            </div>
              <p style="display:inline-block;margin-top:20px;">
                  Sorteer op:
                  <div style="margin-left:10px;" class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#">Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      <li role="separator" class="divider"></li>
                        <li><a href="#">Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      <li role="separator" class="divider"></li>
                        <li><a href="#">Beoordeling verkoper <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Beoordeling verkoper <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      <li role="separator" class="divider"></li>
                        <li><a href="#">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                    </ul>
                  </div>
              </p>

              <p></p>
          </div>
          <?php
            $count = 0;
            foreach($voorwerpenQuery as $row)
            {
          ?>
            <div class="col-sm-6 col-md-6 col-lg-12 col-sm-6">
              <div class="row item-thumb">
                <div class="col-lg-3">
                  <img src="images/vliegtuig.png">
                </div>
                <div class="col-lg-9">
                  <h3 style="font-size:18px;"><?php echo $row['titel']?></h3>

                  <h3 style="font-size:14px;" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>"></h3>
                  <p>Start prijs: <strong><?php echo $row['startprijs']?></strong></p>
                  <p><a href="#" class="btn btn-orange widebutton" role="button">Bieden</a></p>

                </div>
              </div>
            </div>
          <?php
            $count++;

              if($count == 4){

                echo '<div class="clearfix visible-lg-block"></div>';
                $count = 0;
              }
            }
          ?>
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
          //clearInterval(x);
          $( this ).text("EXPIRED");
        }
    });

  }, 1000);

  $('.dropdown-toggle').dropdown()

  </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
