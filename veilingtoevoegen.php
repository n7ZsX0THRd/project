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
      <link href="css/login.css" rel="stylesheet">
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
            <div class="col-lg-12">

              <form class="form-group">
                <label for="naam">naam</label>
                <input value="Naam" id="naam"></input>

              </form>
            <
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
