<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$rootRubriek = -1;
$rubriekID = $rootRubriek;
$filter = 'price_asc';

$available_filters = array('price_asc','price_desc','rate_asc','rate_desc','time_asc','time_desc','count_asc','count_desc');

if ($_SERVER['REQUEST_METHOD'] == 'GET'){ // Disabled, doesn't work with POST Request from search form
  if(isset($_GET['rubriek']) && !empty($_GET['rubriek']))
  {
      $dbs = $db->prepare("SELECT TOP(1) rubrieknummer FROM Rubriek WHERE rubrieknummer = ?");
      $dbs->execute(array(htmlspecialchars($_GET['rubriek'])));
      $resultCount = count($dbs->fetchAll());

      if($resultCount == 1)
        $rubriekID = $_GET['rubriek'];
  }
  if(isset($_GET['filter']) && !empty($_GET['filter']) && in_array($_GET['filter'],$available_filters)){
    $filter = $_GET['filter'];
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

$filter_SQL = '';
$search_SQL = '';

$search_Value = '';

$voorwerpCountQuery = $db->prepare("SELECT
v.voorwerpnummer,
v.looptijdeinde
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
	)".$search_SQL." AND v.looptijdeinde > GETDATE()");

$voorwerpCountQuery->execute(array($rubriek['rubrieknummer']));

$voorwerpenCount = count($voorwerpCountQuery->fetchAll());

$lastPage = floor($voorwerpenCount/30) - 1;

$pageNumber = 1;
if(isset($_GET['page'])){
  if($_GET['page'] >= 1 && $_GET['page'] <= $lastPage)
  {
    $pageNumber = $_GET['page'];
  }
}

//$available_filters = array('price_asc','price_desc','rate_asc','rate_desc','time_asc','time_desc','count_asc','count_desc');

//if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(isset($_GET['search']) && !empty($_GET['search'])){

    $search_SQL = "AND v.titel LIKE '%".$_GET['search']."%'";
    $search_Value = $_GET['search'];
  }

//}
switch ($filter) {
    case 'price_asc':
          $filter_SQL = 'v.startprijs ASC';
        break;
    case 'price_desc':
          $filter_SQL = 'v.startprijs DESC';
        break;
    case 'time_asc':
          $filter_SQL = 'v.looptijdeinde ASC';
        break;
    case 'time_desc':
          $filter_SQL = 'v.looptijdeinde DESC';
        break;
}


$voorwerpSelectSQL = "SELECT
v.voorwerpnummer,
v.titel,
v.startprijs,
vir.rubrieknummer,
r.parentRubriek,
v.looptijdeinde,
Foto.bestandsnaam

FROM Voorwerp v
	JOIN
		VoorwerpInRubriek vir
			ON v.voorwerpnummer = vir.voorwerpnummer
	JOIN
		Rubriek r
			ON r.rubrieknummer = vir.rubrieknummer
  CROSS APPLY
        (
        SELECT  TOP 1 Bestand.bestandsnaam
        FROM    Bestand
        WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
        ) Foto
	WHERE EXISTS
	(
	 SELECT rubrieknummer FROM
	 Rubriek
     WHERE (dbo.fnRubriekIsAfstammelingVan(rubrieknummer,?) = 1 OR rubrieknummer = ?)
	   AND vir.rubrieknummer = rubriek.rubrieknummer
	)
".$search_SQL."  AND v.looptijdeinde > GETDATE()
ORDER BY ".$filter_SQL." ,v.titel
OFFSET 30*? ROWS
FETCH NEXT 30 ROWS ONLY";


$voorwerpenQuery = $db->prepare($voorwerpSelectSQL);

$voorwerpenQuery->execute(array($rubriek['rubrieknummer'],$rubriek['rubrieknummer'],$pageNumber-1)); // RUBRIEK ID, START NUMBER, END NUMBER

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Rubriek - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
  </head>
  <body>

    <?php include 'php/includes/header.php' ?>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-lg-2 col-sm-4 sidebar" >
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
            <form method="GET" action="rubriek.php">
              <input type="hidden" name="rubriek" value="<?php echo $rubriek['rubrieknummer']; ?>">
              <input type="hidden" name="filter" value="<?php echo $filter; ?>">
              <div class="input-group">
                <input type="text" class="form-control" name="search" value="<?php echo $search_Value?>" placeholder="Waar ben je naar op zoek?">
                <span class="input-group-btn">
                  <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                </span>
              </div>
            </form>
              <p style="display:inline-block;margin-top:20px;">
                  Sorteer op:
                  <div style="margin-left:10px;" class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <?php
                      switch ($filter) {
                          case 'price_asc':
                                echo 'Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes"></span>';
                              break;
                          case 'price_desc':
                                echo 'Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
                              break;
                          case 'time_asc':
                                echo 'Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes"></span>';
                              break;
                          case 'time_desc':
                                echo 'Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
                              break;
                      }
                      ?>
                      <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=price_asc&search=<?php echo $search_Value; ?>">Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=price_desc&search=<?php echo $search_Value; ?>">Hoogste bod <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      <li role="separator" class="divider"></li>
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=time_asc&search=<?php echo $search_Value; ?>">Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=time_desc&search=<?php echo $search_Value; ?>">Resterende tijd <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                        <!--
                      <li role="separator" class="divider"></li>
                        <li><a href="#">Beoordeling verkoper <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Beoordeling verkoper <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      <li role="separator" class="divider"></li>
                        <li><a href="#">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="#">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>
                      -->
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
                  <p>Start prijs: <strong>&euro;<?php echo $row['startprijs']?></strong></p>
                  <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bieden</a></p>

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
        <div class="row page-navigation">
          <p>
            <?php
            if($voorwerpenCount > 30)
            {
              if($pageNumber >=2){
                ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo ($pageNumber-1); ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>"> <i class="noselect"><span class="glyphicon glyphicon-triangle-left"></span> Vorige</i></a>
                <?php
              }
            ?>
            <?php

            if($lastPage >= 2){
              ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=1&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == 1) ? 'selected' : '' ;?>">01</i></a>
              <?php
            }

            if($lastPage >= 5)
            {
              if($pageNumber - 5 >= 0 ){
                echo '...';
              }
              for($i = $pageNumber - 2; $i < $pageNumber+ 3; $i++)
              {
                if($i >= 2 && $i <= $lastPage -1)
                {
                  ?>
                  <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo $i;?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == $i) ? 'selected' : '' ;?>"><?php echo sprintf("%02d", $i)?></i></a>
                  <?php
                }
              }
              if($lastPage - 4 >= $pageNumber ){
                echo '...';
              }
            }

            if($lastPage >= 2){
              ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo $lastPage ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == $lastPage) ? 'selected' : '' ;?>"><?php echo sprintf("%02d", $lastPage); ?></i></a>
              <?php
            }
            ?>
            <?php
              if($pageNumber < $lastPage){
                ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo ($pageNumber+1); ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>"> <i class="noselect">Volgende <span class="glyphicon glyphicon-triangle-right"></span></i></a>
                <?php
              }
            }
            ?>
          </p>
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
          $( this ).text("Gesloten");
        }
    });

  }, 1000);

  $('.dropdown-toggle').dropdown()

  </script>
  <script src="js/jquery.sticky.js"></script>
  <script>
    $(document).ready(function(){
      $("#sticky_sidebar").sticky({topSpacing:70});
    });
    </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
