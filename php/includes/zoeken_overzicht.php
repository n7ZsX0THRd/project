<?php
/*
  iProject Groep 2
  30-05-2017

  file: zoeken_overzicht.php
  purpose:
  Load auctions 
*/
session_start();

include_once ('../../php/database.php');
include_once ('../../php/user.php');
pdo_connect();


$rootRubriek = -1;// Root rubriek
$rubriekID = $rootRubriek; // Set default rubriekID to rootRubriek
$filter = 'price_asc'; // Set default filter price_asc


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


$filter_SQL = '';
$search_SQL = '';
$search_Value = '';
// Default search_value search_sql and filter_sql

// If search isset, set search_sql
if(isset($_GET['search']) && !empty($_GET['search'])){

  $search_SQL = "AND v.titel LIKE '%". str_replace("'", "''", $_GET['search'])."%'";
  $search_Value = $_GET['search'];
}

//Switch over filter
switch ($filter) {
    case 'price_asc':
          $filter_SQL = 'hoogsteBod ASC , v.startprijs ASC';
        break;
    case 'price_desc':
          $filter_SQL = 'hoogsteBod DESC , v.startprijs DESC';
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
$voorwerpCountQuery = $db->prepare("SELECT DISTINCT
	  v.voorwerpnummer,
	  v.titel,
	  v.startprijs,
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
".$search_SQL."  AND v.looptijdeinde > GETDATE() AND v.inactief = 0
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
$voorwerpSelectSQL = "SELECT DISTINCT
	  v.voorwerpnummer,
	  v.titel,
	  v.startprijs,
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
".$search_SQL."  AND v.looptijdeinde > GETDATE() AND v.inactief = 0
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

  <div class="row">

    <?php
      $count = 0;

      // Foreach auction, show auctions
      foreach($voorwerpenQuery as $row)
      {
    ?>
      <div class="col-sm-12 col-md-12 col-lg-12 col-sm-12">
        <div class="row item-thumb">
          <div class="col-lg-3 col-xs-3 col-sm-4 col-md-4" >
            <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>">
              <div class="item-row-image" style="background-image:url(<?php echo $row['bestandsnaam'];?>);">
              </div>
            </a>
          </div>
          <div class="col-lg-9 col-xs-9 col-sm-8 col-md-8" style="position:relative;flex: 1;">
            <a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>"><h3 class="item-row-titel"><?php echo $row['titel']?></h3></a>

            <h3 style="font-size:14px;" class="orange" id="looptijdeinde" data-looptijd="<?php echo $row['looptijdeinde']?>">&nbsp;</h3>
            <p>Start prijs: <strong>&euro;<?php echo number_format($row['startprijs'], 2, ',', '.')?></strong></p>
            <p>Hoogste bod: <strong><?php echo ($row['hoogsteBod'] != null) ? '&euro;'.number_format($row['hoogsteBod'], 2, ',', '.'): 'Er is nog niet geboden';?></strong></p>
            <p>Aantal keer geboden: <strong><?php echo $row['aantalBiedingen'];?></strong></p>
            <p style="position:absolute; bottom:0px;right:0px;width:150px;"><a href="veiling.php?voorwerpnummer=<?php echo $row['voorwerpnummer']; ?>" class="btn btn-orange widebutton" role="button">Bieden</a></p>

          </div>
        </div>
      </div>
    <?php
      $count++;
      }
      if($count == 0)
      {
        ?>
          <p class="bg-warning" style="padding:5px;margin-top:15px;">
              Helaas, er zijn geen veilingen gevonden.<br>
          </p>
        <?php
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
  <script>
  $('.dropdown-toggle').dropdown();
  </script>
