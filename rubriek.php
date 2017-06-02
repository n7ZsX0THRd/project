<?php
/*
  iProject Groep 2
  30-05-2017

  file: rubriek.php
  purpose:
  Show rubrieks,
  and search for auctions.
*/
session_start();

include_once ('php/database.php');
include_once ('php/user.php');
pdo_connect();
// include database and user functions
// Connect to database

$rootRubriek = -1;// Root rubriek
$rubriekID = $rootRubriek; // Set default rubriekID to rootRubriek
$filter = 'price_asc'; // Set default filter price_asc

//Array of available_filters
$available_filters = array('price_asc','price_desc','rate_asc','rate_desc','time_asc','time_desc','count_asc','count_desc');

if ($_SERVER['REQUEST_METHOD'] == 'GET'){

  // Check if rubriek isset in GET
  if(isset($_GET['rubriek']) && !empty($_GET['rubriek']))
  {
      $dbs = $db->prepare("SELECT TOP(1) rubrieknummer FROM Rubriek WHERE rubrieknummer = ?");
      $dbs->execute(array(htmlspecialchars($_GET['rubriek'])));
      $resultCount = count($dbs->fetchAll());

      // check if rubriek in get is a valid rubriek
      if($resultCount == 1)
        $rubriekID = $_GET['rubriek'];
      // if valid rubriek, change current rubriek to rubriek from get
  }
  // if filter isset in get, and exists in array of available_filters update filter to filter from get
  if(isset($_GET['filter']) && !empty($_GET['filter']) && in_array($_GET['filter'],$available_filters)){
    $filter = $_GET['filter'];
  }
}

// Select currentRubriek info from database
$rubriekQuery = $db->prepare("SELECT TOP(1) rubrieknummer,rubrieknaam,parentRubriek,volgnr FROM Rubriek WHERE rubrieknummer = ?");
$rubriekQuery->execute(array(htmlspecialchars($rubriekID)));
$rubriek = $rubriekQuery->fetchAll()[0];

// Select child rubrieks from current rubriek.
$childrenRubriekenQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam FROM Rubriek WHERE parentRubriek = ? ORDER BY volgnr ASC, rubrieknaam ASC");
$childrenRubriekenQuery->execute(array(htmlspecialchars($rubriekID)));
$childrenRubrieken = $childrenRubriekenQuery->fetchAll();

//Select currentRubriek parents
$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars($rubriekID)));
$breadCrumb = $breadCrumbQuery->fetchAll();

$filter_SQL = '';
$search_SQL = '';
$search_Value = '';
// Default search_value search_sql and filter_sql

// If search isset, set search_sql
if(isset($_GET['search']) && !empty($_GET['search'])){

  $search_SQL = "AND v.titel LIKE '%".$_GET['search']."%'";
  $search_Value = $_GET['search'];
}

//Switch over filter
switch ($filter) {
    case 'price_asc':
          $filter_SQL = 'hoogsteBod ASC';
        break;
    case 'price_desc':
          $filter_SQL = 'hoogsteBod DESC';
        break;
    case 'time_asc':
          $filter_SQL = 'v.looptijdeinde ASC';
        break;
    case 'time_desc':
          $filter_SQL = 'v.looptijdeinde DESC';
        break;
    case 'count_asc':
          $filter_SQL = 'aantalBiedingen ASC';
        break;
    case 'count_desc':
          $filter_SQL = 'aantalBiedingen DESC';
        break;
}
// Set sql part for specific filter
$voorwerpCountQuery = $db->prepare("SELECT
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
  LEFT JOIN
    		Bod b
    			ON b.voorwerpnummer = v.voorwerpnummer
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
GROUP BY
	v.voorwerpnummer,
	v.titel,
	v.startprijs,
	vir.rubrieknummer,
	r.parentRubriek,
	v.looptijdeinde,
	Foto.bestandsnaam");

$voorwerpCountQuery->execute(array($rubriekID,$rubriekID));

$voorwerpenCount = count($voorwerpCountQuery->fetchAll());
// Select count of found auctions


$lastPage = floor($voorwerpenCount/30) - 1;
//Calculate amount of pages for navigation

$pageNumber = 1;// Set pageNumber to 1 as default
if(isset($_GET['page'])){
  if($_GET['page'] >= 1 && $_GET['page'] <= $lastPage)
  {
    $pageNumber = $_GET['page'];
    // Set pageNumber to pageNumber from get if valid
  }
}
// Select auctions from database based on filters
$voorwerpSelectSQL = "SELECT
  v.voorwerpnummer,
  v.titel,
  v.startprijs,
  vir.rubrieknummer,
  r.parentRubriek,
  v.looptijdeinde,
  Foto.bestandsnaam,
  dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod,
  COUNT(b.voorwerpnummer) AS aantalBiedingen
FROM Voorwerp v
	JOIN
		VoorwerpInRubriek vir
			ON v.voorwerpnummer = vir.voorwerpnummer
	JOIN
		Rubriek r
			ON r.rubrieknummer = vir.rubrieknummer
  LEFT JOIN
    		Bod b
    			ON b.voorwerpnummer = v.voorwerpnummer
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
GROUP BY
	v.voorwerpnummer,
	v.titel,
	v.startprijs,
	vir.rubrieknummer,
	r.parentRubriek,
	v.looptijdeinde,
	Foto.bestandsnaam
ORDER BY ".$filter_SQL." ,v.titel
OFFSET 30*? ROWS
FETCH NEXT 30 ROWS ONLY";


$voorwerpenQuery = $db->prepare($voorwerpSelectSQL);
$voorwerpenQuery->execute(array($rubriek['rubrieknummer'],$rubriek['rubrieknummer'],$pageNumber-1));
// RUBRIEK ID, START NUMBER, END NUMBER

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php
          include 'php/includes/default_header.php';
          // Include default head
        ?>

        <title>Rubriek - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
  </head>
  <body>

    <?php
      include 'php/includes/header.php';
      // Include navigation
    ?>

<div class="container">
  <div class="row">
    <div class="col-md-4 col-lg-2 col-sm-4 sidebar" >
      <h3></h3>
      <ul class="menubar">
        <?php
        // Check if current rubriek not equal to parent rubriek
        if($rubriek['rubrieknummer'] != -1){

            // if currentRubriek has childrubrieks == false
            if(count($childrenRubrieken) == 0)
            {
              // Select parent rubriek for title
              $parentQuery = $db->prepare("SELECT rubrieknummer, rubrieknaam, parentRubriek FROM Rubriek WHERE rubrieknummer = ? ORDER BY volgnr ASC, rubrieknaam ASC");
              $parentQuery->execute(array(htmlspecialchars($rubriek['parentRubriek'])));
              $parent = $parentQuery->fetchAll();
              if(count($parent) != 0)
              {
              ?>
                <li class="toggle-sub active">
                  <a href="rubriek.php?rubriek=<?php echo $parent[0]['rubrieknummer']; ?>"><?php echo $parent[0]['rubrieknaam']; ?></a>
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
          // Show title with content 'Rubrieken'
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
        // If breadCrumb exists
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
                  // Show default breadCrumb for rubriek page

                  // foreach breadCrumb add breadCrumbPart
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
            <!-- SEARCH FORM END -->
              <p style="display:inline-block;margin-top:20px;">
                  Sorteer op:
                  <div style="margin-left:10px;" class="btn-group" role="group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <?php
                      // Switch over current field, and set as selected in button
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
                          case 'count_asc':
                                echo 'Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes"></span>';
                              break;
                          case 'count_desc':
                                echo 'Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
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
                        -->
                      <li role="separator" class="divider"></li>
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=count_asc&search=<?php echo $search_Value; ?>">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes"></span></a></li>
                        <li><a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=count_desc&search=<?php echo $search_Value; ?>">Aantal biedingen <span class="glyphicon glyphicon-sort-by-attributes-alt"></span></a></a></li>

                    </ul>
                  </div>
              </p>

              <p></p>
          </div>
          <?php
            $count = 0;

            // Foreach auction, show auctions
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
                  <p>Start prijs: <strong>&euro;<?php echo number_format($row['startprijs'], 2, ',', ' ')?></strong></p>
                  <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', ' '): 'Er is nog niet geboden';?></strong></p>
                  <p>Aantal keer geboden: <strong><?php echo $row['aantalBiedingen'];?></strong></p>
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
            // If $voorwerpenCount > 30 multiple pages exists, show navigation
            if($voorwerpenCount > 30)
            {
              // if pageNumber >=2 show 'previous' button
              if($pageNumber >=2){
                ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo ($pageNumber-1); ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>"> <i class="noselect"><span class="glyphicon glyphicon-triangle-left"></span> Vorige</i></a>
                <?php
              }
            ?>
            <?php
            // if more than 2 pages exists , show '01' button
            if($lastPage >= 2){
              ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=1&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == 1) ? 'selected' : '' ;?>">01</i></a>
              <?php
            }

            if($lastPage >= 5)
            {
              // Show dots between navigation buttons and first page buttons
              if($pageNumber - 5 >= 0 ){
                echo '...';
              }
              // Show 5 buttons for navigation around current page
              for($i = $pageNumber - 2; $i < $pageNumber+ 3; $i++)
              {
                if($i >= 2 && $i <= $lastPage -1)
                {
                  ?>
                  <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo $i;?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == $i) ? 'selected' : '' ;?>"><?php echo sprintf("%02d", $i)?></i></a>
                  <?php
                }
              }
              // Show dots between navigation buttons and last page buttons
              if($lastPage - 4 >= $pageNumber ){
                echo '...';
              }
            }

            // if more than 2 pages exists, show 'lastPage' button
            if($lastPage >= 2){
              ?>
                <a href="rubriek.php?rubriek=<?php echo $rubriek['rubrieknummer'];?>&page=<?php echo $lastPage ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>" class="circle noselect"><i class="circle noselect <?php echo ($pageNumber == $lastPage) ? 'selected' : '' ;?>"><?php echo sprintf("%02d", $lastPage); ?></i></a>
              <?php
            }
            ?>
            <?php
             // if currentPage < lastPage show 'next' page button
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
<?php
  include 'php/includes/footer.php';
  //include footer
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

  $('.dropdown-toggle').dropdown()

  </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
