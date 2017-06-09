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

$search_Value = '';
$pageNumber = 1;// Set pageNumber to 1 as default


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
  // Default search_value search_sql and filter_sql

  // If search isset, set search_sql
  if(isset($_GET['search']) && !empty($_GET['search'])){

    $search_Value = $_GET['search'];
  }
  if(isset($_GET['page'])){
    if($_GET['page'] >= 1 && is_numeric($_GET['page']))
    {
      $pageNumber = $_GET['page'];
      // Set pageNumber to pageNumber from get if valid
    }
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
        <link href="css/clock.css" rel="stylesheet">
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
              <div class="row navigation-row fix">
                  <h1 style="margin-bottom: 10px" >Rubrieken
                    <?php if(getLoggedInUser($db) != null && (getLoggedInUser($db)['typegebruiker'] == 2 || getLoggedInUser($db)['typegebruiker'] == 3)){?>
                    <a style="float:right;" class="btn btn-orange" href="veilingtoevoegen.php">Veiling Plaatsen</a>
                    <?php } ?>
                  </h1>
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
              <div class="col-lg-12 col-xs-12 col-sm-12 col-md-12"  style="border-bottom:2px solid #E6E6E6; padding-left:0px;padding-right:0px;">
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
            </div>
             <div  id="zoeken_overzicht">
               <div class='uil-clock-css' style='transform:scale(0.4); margin-left:auto;margin-right:auto;margin-top:10vh;'><div class="clock"></div><div class="ptr1"></div><div class="ptr2"></div></div>
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

  $('.dropdown-toggle').dropdown();

  $(document).ready(function(){
    //$( "#zoeken_overzicht" ).load( "php/includes/zoeken_overzicht.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=price_asc&search=<?php echo $search_Value; ?>" );
    var link =  "php/includes/zoeken_overzicht.php?rubriek=<?php echo $rubriek['rubrieknummer']; ?>&page=<?php echo $pageNumber; ?>&filter=<?php echo $filter; ?>&search=<?php echo $search_Value; ?>";
    $.get(
        link,
        function(data) {
            $("#zoeken_overzicht").fadeOut( "slow", function() {
              $("#zoeken_overzicht").html(data).fadeIn('slow');
            });
        },
        "html"
    );
  });
  </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
