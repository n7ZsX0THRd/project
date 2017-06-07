<?php
/*
  iProject Groep 2
  30-05-2017

  file: account.php
  purpose:
  Show shortcuts for user
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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include 'php/includes/default_header.php'; ?>
      <link href="css/dashboard.css" rel="stylesheet">
      <title>Mijn Account - Eenmaal Andermaal</title>
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
      <div class="col-md-9 col-lg-10 col-sm-8">
        <div class="container-fluid content_col">
          <div class="row">
              <h1 style="margin-bottom: 4%" > Mijn Account </h1>
              <div class="row item-row">
                <!--  My BIDS -->
                <div onclick="window.location='biedingen.php';" class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <img class="account_icons"src=
                    "images/money.png"/>
                    <div class="caption captionfix">
                      <h3 style="text-align: center" >Biedingen</h3>
                    </div>
                  </div>
                </div>
                <!--  My BIDS END -->
                <!--  My Favorites -->
                <div onclick="window.location='veilingen.php';"  class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <img class="account_icons" src=
                    "images/star.png"/>
                    <div class="caption captionfix">
                      <h3 style="text-align: center" >Veilingen</h3>
                    </div>
                  </div>
                </div>
                <!--  My Favorites END -->
                <!--  Settings -->
                <div onclick="window.location='profiel.php';" class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <img class="account_icons" src=
                    "images/menu.png"/>
                    <div class="caption captionfix">
                      <h3 style="text-align: center" >Gegevens</h3>
                    </div>
                  </div>
                </div>
                <!--  Settings END -->
                <!--  Sign Off -->
                <div onclick="window.location='php/logout.php';"  class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <img class="account_icons" src=
                    "images/logout.png"/>
                    <div class="caption captionfix">
                      <h3 style="text-align: center" >Uitloggen</h3>
                    </div>
                  </div>
                </div>
                <!--  Sign End -->
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
  </body>
</html>
