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

</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
