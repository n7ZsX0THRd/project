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

$_SESSION['menu']['sub'] = 'dr';
// Set session for sidebar menu,
// dr -> Direct Regelen
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
          <div class="row navigation-row">
              <p>
                <a href="index.php">
                  <span class="glyphicon glyphicon-home "></span>
                </a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="">Direct Regelen</a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="">Verkopen</a>
              </p>
          </div>
          <div class="row content_top_offset">

              <div class="col-lg-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-7">
                        <label for="exampleInputEmail1">Titel</label>
                        <input name="p_adres2" type="text" class="form-control" id="exampleInputEmail1" placeholder="Titel (verplicht)" value="">
                        <p></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label for="exampleInputFile">Beschrijving</label>

                          <textarea class="form-control" rows="10" style="max-width:100%;" placeholder="Beschrijving (verplicht)" name="p_biografie"  maxlength="1024" ></textarea>
                        </div>
                        <div class="form-group">

                         <button type="button" data-toggle="modal" data-target=".bs-example-modal-lg" class="btn btn-orange">Rubriek toevoegen</button>   <p><i>Tot 2 rubrieken gratis, minimaal 1</i></p>
                       </div>
                      </div>
                    </div>
                  </div>
              </div>
          </div>
      </div>
      <!-- CONTAINER END -->
    </div>
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
              <div class="input-group">
                <input type="text" id="rubriekSearchInput" class="form-control" placeholder="Zoek een rubriek">
                <span class="input-group-btn">
                  <button class="btn btn-orange" id="searchButton" type="button">Zoek</button>
                </span>
              </div>
              <div id="result_search">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div>
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
    <script>
    $( "#rubriekSearchInput" ).keydown(function(){
        console.log("KEY UP SEARCH BUTTON");
          var search = $("#rubriekSearchInput").val();
          if(search.length > 1)
            $( "#result_search" ).load( "php/includes/search_rubriek.php?search=" + search);
    });
    </script>
  </body>
</html>
