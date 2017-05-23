</<?php session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect(); ?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="bootstrap/favicon.ico">

    <title>Normal Page</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="bootstrap/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="bootstrap/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="css/stylesheet.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet">
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>


    <div class="container">
      <div class="col-md-3 col-lg-2 col-sm-4 sidebar">
        <?php
          if(isUserLoggedIn($db))
            include 'php/includes/sidebar.php';
          else {
            ?>
              <h3></h3>
              <ul class="menubar">
                <li class="toggle-sub active">
                  <a href="">Rubrieken</a>
                </li>
                <ul class="sub">
                  <?php
                    foreach($childrenRubrieken as $row)
                    {
                      ?>
                      <li>
                        <a href="rubriek.php?rubriek=<?php echo $row['rubrieknummer']; ?>"><?php echo $row['rubrieknaam']; ?></a>
                      </li>
                      <?php
                    }
                  ?>
                </ul>
              </ul>
            <?php
          }
        ?>
      </div>
      <div class="col-md-9 col-lg-10 col-sm-8">
        <div class="container-fluid content_col">
        <div class="row">
            <h1 style="margin-bottom: 4%" > Acties </h1>
            <div class="row item-row">
              <div  style="cursor:hand" onclick="window.location='index.php';" class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                <div class="thumbnail">
                  <img class="account_icons"src=
                  "images/money.png"/>
                  <div class="caption captionfix">
                    <h3 style="text-align: center" >Mijn biedingen</h3>
                  </div>
                </div>
              </div>
              <div style="cursor:hand" onclick="window.location='index.php';"  class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                <div class="thumbnail">
                  <img class="account_icons" src=
                  "images/star.png"/>
                  <div class="caption captionfix">
                    <h3 style="text-align: center" >Mijn favorieten</h3>
                  </div>
                </div>
              </div>
              <div style="cursor:hand" onclick="window.location='profiel.php';" class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                <div class="thumbnail">
                  <img class="account_icons" src=
                  "images/menu.png"/>
                  <div class="caption captionfix">
                    <h3 style="text-align: center" >Instellingen</h3>
                  </div>
                </div>
              </div>
                <div style="cursor:hand" onclick="window.location='php/logout.php';"  class="col-sm-6 col-md-6 col-lg-3 col-sm-6">
                  <div class="thumbnail">
                    <img class="account_icons" src=
                    "images/logout.png"/>
                    <div class="caption captionfix">
                      <h3 style="text-align: center" >Uitloggen</h3>
                    </div>
                  </div>
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
