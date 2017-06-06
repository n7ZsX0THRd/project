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

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  var_dump($_POST);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include 'php/includes/default_header.php'; ?>
      <link href="css/veiling.css" rel="stylesheet">
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
            <form action="" method="post">
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
                         <div class="form-group" id="notification">
                         </div>
                         <div class="form-group" id="resultedRubriek">


                         </div>
                       </div>
                      </div>
                    </div>
                  </div>
              </div>
              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-4">
                    <input type='file' name="file" onchange="readURL(this,'#blah');" />
                    <input type='file' name="file2" onchange="readURL(this,'#blah2');" />
                    <input type='file' name="file3" onchange="readURL(this,'#blah3');" />
                    <input type='file' name="file4" onchange="readURL(this,'#blah4');" />
                  </div>
                  <div class="col-lg-8">
                    <div class="row">
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="blah" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="blah2" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="blah3" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="blah4" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
      </div>
      <!-- CONTAINER END -->
    </div>
    <div id="rubriek_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Rubriek zoeken</h4>
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
    jQuery.fn.exists = function(){ return this.length > 0; }

    $( "#searchButton" ).click(function(event){
          var search = $("#rubriekSearchInput").val();
          if(search.length > 1)
            $( "#result_search" ).load( "php/includes/search_rubriek.php?search=" + search);
    });
    $("#rubriekSearchInput").keyup(function(event){
        if(event.keyCode == 13){
            $("#searchButton").click();
        }
    });
    $(document).on("click", '#add_rubriek', function(event) {
        //alert("new link clicked!");

        if ($('#' + $(event.target).data( "rubriekid" )).exists() == false) {
          $('#resultedRubriek').append('<div id="' + $(event.target).data( "rubriekid" ) + '"><input type="hidden" name="rubrieken[]" value="' + $(event.target).data( "rubrieknaam" ) + '" /><p><a class="btn btn-default" id="remove" style="margin-right:10px;"><span class="glyphicon glyphicon-minus"></span></a>' + $(event.target).data( "parentnaam" ) + ' <span class="glyphicon glyphicon-menu-right"></span> ' + $(event.target).data( "rubrieknaam" ) + '</p></div>');
          $('#result_search').empty();
          $('#rubriekSearchInput').val("");
          $('#notification').empty();
        }
        else {
          $('#notification').append('<p class="bg-warning" style="padding:5px;">Je kunt niet twee keer hetzelfde rubriek toevoegen</p>');

        }
    });
    $(document).on("click", '#remove', function(event) {
        //alert("new link clicked!");
        $(event.target).parent().parent().remove();
    });
    function readURL(input,target) {
       if (input.files && input.files[0]) {
           var reader = new FileReader();

           reader.onload = function (e) {
               $(target)
                   .css("background-image", "url("+e.target.result+")")
                   .css("background-size", "contain")
                   .css("background-position", "center")
                   .css("background-repeat", "no-repeat")
                   .css("width","100%")
                   .css("padding-bottom","100%")
                   .css("border","1px solid #E7E7E7");
           };

           reader.readAsDataURL(input.files[0]);
       }
   }
    </script>
  </body>
</html>
