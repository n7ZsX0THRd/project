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

define ("MAX_SIZE","9000");
function getExtension($str)
{
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
}

// If user is not logged In redirect to homepage
if(isUserLoggedIn($db) == false)
  header("Location: index.php");

$_SESSION['menu']['sub'] = 'dr';
// Set session for sidebar menu,
// dr -> Direct Regelen

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  //var_dump($_POST);
  $_POST['vt_seller'] = getLoggedInUser($db)['gebruikersnaam'];

  $uploaddir = "uploads/"; //image upload directory

  //set the image extentions
  $valid_formats = array("jpg", "png", "bmp","jpeg");
  $index = 0;
  $succesfullUploadedPhotos = false;
  foreach ($_FILES['vt_images']['name'] as $name => $value)
  {
    $_POST['vt_images'][$index] = NULL;

    if(strlen($value) > 3)
    {

      $filename = stripslashes($_FILES['vt_images']['name'][$name]);
      $size=filesize($_FILES['vt_images']['tmp_name'][$name]);
      //get the extension of the file in a lower case format
        $ext = getExtension($filename);
        $ext = strtolower($ext);

       if(in_array($ext,$valid_formats))
       {
         if ($size < (MAX_SIZE*1024))
         {
         $image_name=time().$filename;
         echo "<img src='".$uploaddir.$image_name."' class='imgList'>";
         $newname=$uploaddir.$image_name;

         if (move_uploaded_file($_FILES['vt_images']['tmp_name'][$name], $newname))
         {
           $time=time();
             //insert in database
         //mysql_query("INSERT INTO user_uploads(image_name,user_id_fk,created) VALUES('$image_name','$session_id','$time')");
            $_POST['vt_images'][$index] = $uploaddir.$image_name;
            $succesfullUploadedPhotos = true;
         }
         else
         {
          echo '<span class="imgList">You have exceeded the size limit! so moving unsuccessful! </span>';
          $succesfullUploadedPhotos = false;
          }

         }
         else
         {
          echo '<span class="imgList">You have exceeded the size limit!</span>';
          $succesfullUploadedPhotos = false;

         }

        }
        else
       {
           echo '<span class="imgList">Unknown extension!</span>';
           $succesfullUploadedPhotos = false;
       }
    }

     $index++;
 }

 if($succesfullUploadedPhotos)
 {
   $result = create_auction($_POST,$db);
   var_dump($result);
 }

  /*
  print '<br><br>';
  var_dump($result->getCode());
  print '<br><br>';
  var_dump($result);
  */
}

$queryPaymentMethods = $db->query("SELECT ID,betalingswijze FROM Betalingswijzen ORDER BY ID ASC");
$queryCountries = $db->query("SELECT lnd_Code,lnd_Landnaam FROM Landen");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include 'php/includes/default_header.php'; ?>
      <link href="css/veiling.css" rel="stylesheet">
      <title>Verkopen - Eenmaal Andermaal</title>

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
          <div class="row">
            <form action="" method="post" enctype="multipart/form-data">
              <div class="col-lg-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-12">
                        <label for="vt_title">Titel</label>
                        <input name="vt_title" type="text" class="form-control" id="vt_title" placeholder="Titel (verplicht)" value="">
                        <p></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label for="vt_description">Beschrijving</label>
                          <textarea class="form-control" rows="10" id="vt_description" name="vt_description" style="max-width:100%;" placeholder="Beschrijving (verplicht)" maxlength="1024" ></textarea>
                        </div>
                        <!--
                          1,3,5,7,10
                        -->
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="vt_startPrice">Startprijs</label>
                            <div class="input-group">
                               <span class="input-group-addon">€</span>
                               <input class="form-control" type="number" id="vt_startPrice" required name="vt_startPrice" value="0.00" step="any">
                             </div>
                          </div>
                          <div class="col-lg-6">
                            <label for="vt_auctionTime">Looptijd</label>
                            <select name="vt_auctionTime" class="form-control" id="vt_auctionTime">
                              <option disabled>Looptijd</p>
                              <option value="1">1 dag</option>
                              <option value="3">3 dagen</option>
                              <option value="5">5 dagen</option>
                              <option value="7" selected>7 dagen</option>
                              <option value="10">10 dagen</option>
                            </select>
                          </div>
                        </div>
                        <p></p>
                        <hr>
                        <div class="form-group">
                           <label>Rubrieken</label>
                           <p><i><button type="button" data-toggle="modal" data-target=".bs-example-modal-lg" class="btn btn-orange">Rubriek toevoegen</button>   Tot 2 rubrieken gratis, minimaal 1</i></p>
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
                <hr>
                <div class="row">

                  <div class="col-lg-6">
                    <label>Foto's</label>
                    <input type="file" class="btn" name="vt_images[]" onchange="readURL(this,'#f1');" />
                    <input type="file" class="btn" name="vt_images[]" onchange="readURL(this,'#f2');" />
                    <input type="file" class="btn" name="vt_images[]" onchange="readURL(this,'#f3');" />
                    <input type="file" class="btn" name="vt_images[]" onchange="readURL(this,'#f4');" />
                  </div>
                  <div class="col-lg-6">
                    <div class="row">
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="f1" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="f2" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="f3" />
                      </div>
                      <div class="col-lg-6" style="padding-left:0px;padding-right:0px;">
                        <img id="f4" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <hr>
                <div class="row content_top_offset">
                  <div class="col-lg-6">
                    <label for="vt_payment">Betalingswijze</label>
                    <select class="form-control" id="vt_payment" name="vt_payment">
                      <option disabled selected>Betalingswijze</p>
                      <?php
                        foreach($queryPaymentMethods as $row)
                        {
                      ?>
                          <option value="<?php echo $row['ID']; ?>"><?php echo $row['betalingswijze']; ?></option>
                      <?php
                        }
                      ?>
                    </select>
                    <p></p>
                    <label for="vt_paymentInstruction">Betalingsinstructie</label>
                    <input name="vt_paymentInstruction" id="vt_paymentInstruction" type="text" class="form-control" placeholder="Betalingsinstructie" value="">
                    <p></p>
                    <label for="vt_city">Plaatsnaam</label>
                    <input name="vt_city" id="vt_city" type="text" class="form-control" placeholder="Plaatsnaam (verplicht)" value="">
                    <p></p>
                    <label for="vt_zipcode">Postcode</label>
                    <input name="vt_zipcode" id="vt_zipcode" type="text" class="form-control" placeholder="Postcode (verplicht)" value="">
                    <p></p>
                    <label for="vt_country">Land</label>
                    <select class="form-control" id="vt_country" name="vt_country">
                      <option disabled selected>Land</p>
                      <?php
                        foreach($queryCountries as $row)
                        {
                      ?>
                          <option value="<?php echo $row['lnd_Code']; ?>"><?php echo $row['lnd_Landnaam']; ?></option>
                      <?php
                        }
                      ?>
                    </select>
                    <p></p>
                    <label for="vt_send">Verzendkosten</label>
                    <div class="input-group">
                       <span class="input-group-addon">€</span>
                       <input class="form-control" type="number" id="vt_send" required name="vt_send" value="0.00" step="any">
                    </div>
                    <p></p>
                    <label for="vt_sendInstructions">Verzendinstructies</label>
                    <input name="vt_sendInstructions" id="vt_sendInstructions" type="text" class="form-control" placeholder="Verzendinstructies" value="">
                    <p></p>
                  </div>
                </div>
              </div>
              <div class="col-lg-12">
                <hr>
              </div>
              <div class="col-lg-12">
                <div class="text-right">
                  <button type="submit" class="btn btn-orange">Veiling starten</button>
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
              <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
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
          $('#resultedRubriek').append('<div id="' + $(event.target).data( "rubriekid" ) + '"><input type="hidden" name="vt_rubrieken[]" value="' + $(event.target).data( "rubriekid" ) + '" /><p><a class="btn btn-default" id="remove" style="margin-right:10px;"><span class="glyphicon glyphicon-minus"></span></a>' + $(event.target).data( "parentnaam" ) + ' <span class="glyphicon glyphicon-menu-right"></span> ' + $(event.target).data( "rubrieknaam" ) + '</p></div>');
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
