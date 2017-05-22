<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$rootRubriek = -1;
$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars(1)));
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
      <?php
        include 'php/includes/sidebar.php';
      ?>
    </div>
    <div class="col-md-8 col-lg-10 col-sm-8">
      <div class="container-fluid  content_col">
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
