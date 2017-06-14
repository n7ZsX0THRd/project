<?php
/*
  iProject Groep 2
  30-05-2017

  file: veilingtoevoegen.php
  purpose:
  Page to create new auction
*/
session_start();
// Start Session
// Include database connection and user functins
include_once('php/database.php');
include_once('php/user.php');
pdo_connect();
// Connect with database

// Define the maximum size of an image
define ("MAX_SIZE","9000");
// Function to get the file extension of an uploaded image
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

$result = getLoggedInUser($db);

// If logged in user is not a seller or a pending seller account.
// Redirect to register as seller page
if($result['typegebruiker'] == 1 || $result['typegebruiker'] == 4)
{
  header("Location: verkoper_worden.php");
  exit();
}

$_SESSION['menu']['sub'] = 'dr';
// Set session for sidebar menu,
// dr -> Direct Regelen
//set the image extentions
$valid_formats = array("jpg", "png", "bmp","jpeg");
// Allowed file formats

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  //var_dump($_POST);
  $_POST['vt_seller'] = getLoggedInUser($db)['gebruikersnaam'];
  // Add seller to post values;

  $uploaddir = "uploads/"; //image upload directory

  $index = 0;
  $succesfullUploadedPhotos = false;

  if(isset($_FILES['vt_images']) == false)
  { // Check if there are images to uploaded, if not set session for notification
    $_SESSION['warning']['no_images'] = true;
  }
  else {
    // Looop over images to upload,
    foreach ($_FILES['vt_images']['name'] as $name => $value)
    {
      $_POST['vt_images'][$index] = NULL;

      if(strlen($value) > 3)
      {
        // Check if image name with extension is longert than 3 characters
        $filename = stripslashes($_FILES['vt_images']['name'][$name]);
        $size=filesize($_FILES['vt_images']['tmp_name'][$name]);
        //get the extension of the file in a lower case format
          $ext = getExtension($filename);
          $ext = strtolower($ext);

          // Check if uploaded file extension is list of valid formats
         if(in_array($ext,$valid_formats))
         {
             // Check if the size of uploaded file is less than max size
             if ($size < (MAX_SIZE*1024))
             {
               $image_name=time().$filename;
               $newname=$uploaddir.$image_name;
               // create the name for the image

               // Upload file to directory on the server
               if (move_uploaded_file($_FILES['vt_images']['tmp_name'][$name], $newname))
               {
                  $time=time();
                  $_POST['vt_images'][$index] = $uploaddir.$image_name;
                  // Set the list of images in the post var which will be inserted into the database
                  $succesfullUploadedPhotos = true;
               }
               else
               {
                  $_SESSION['warning']['uploadFailed'] = $filename;
                  // Set session for upload failed
                  $succesfullUploadedPhotos = false;
               }

             }
             else
             {
              $_SESSION['warning']['filesize'] = $filename;
              // Set session for incorrect filesize
              $succesfullUploadedPhotos = false;
             }

          }
          else
          {
             $_SESSION['warning']['formaterror'] = $filename;
             // Set session for incorrect fileformat
             $succesfullUploadedPhotos = false;
           }
      }

       $index++;
   }
  }

 $didCreateAuction = false;
 if($succesfullUploadedPhotos)
 {
   // Check if fields match contraints else set session for specific warning
   if(empty($_POST['vt_title']) || strlen(trim($_POST['vt_title'])) < 2 || strlen(trim($_POST['vt_title'])) > 70 || preg_match('/[!\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['vt_paymentInstruction']))
   {
     $_SESSION['warning']['titel_invalid'] = true;
   }
   else if(empty($_POST['vt_description']) || strlen(trim($_POST['vt_description'])) < 10 )
   {
     $_SESSION['warning']['description_invalid'] = true;
   }
   else if(empty($_POST['vt_startPrice']) || is_numeric($_POST['vt_startPrice']) == false || (float)$_POST['vt_startPrice'] < 0)
   {
     $_SESSION['warning']['price_invalid'] = true;
   }
   else if(isset($_POST['vt_rubrieken']) == false || count($_POST['vt_rubrieken']) < 1)
   {
     $_SESSION['warning']['rubrieken_invalid'] = true;
   }
   else if(empty($_POST['vt_paymentInstruction']) == false && (strlen(trim($_POST['vt_paymentInstruction'])) < 1 || strlen(trim($_POST['vt_paymentInstruction'])) > 30 || preg_match('/[!\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['vt_paymentInstruction'])))
   {
     $_SESSION['warning']['paymentinstruction_invalid'] = true;
   }
   else if(empty($_POST['vt_sendInstructions']) == false && (strlen(trim($_POST['vt_sendInstructions'])) < 1 || strlen(trim($_POST['vt_sendInstructions'])) > 30 || preg_match('/[!\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['vt_sendInstructions'])))
   {
     $_SESSION['warning']['sendInstruction_invalid'] = true;
   }
   else if(empty($_POST['vt_city']) || strlen(trim($_POST['vt_city'])) < 1 || strlen(trim($_POST['vt_city'])) > 30 || preg_match('/[!\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['vt_city']))
   {
     $_SESSION['warning']['city_invalid'] = true;
   }
   else if(empty($_POST['vt_zipcode']) || strlen(trim($_POST['vt_zipcode'])) < 1 || strlen(trim($_POST['vt_zipcode'])) > 9 || preg_match('/[!\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['vt_zipcode']))
   {
     $_SESSION['warning']['zipcode_invalid'] = true;
   }
   else if(empty($_POST['vt_send']) || is_numeric($_POST['vt_send']) == false || (float)$_POST['vt_send'] < 0 || (float)$_POST['vt_send'] > 9999.99)
   {
     $_SESSION['warning']['send_invalid'] = true;
   }
   else {

     if(empty($_POST['vt_paymentInstruction']))
      $_POST['vt_paymentInstruction'] = null;
     if(empty($_POST['vt_sendInstructions']))
       $_POST['vt_sendInstructions'] = null;
     // Set paymentInstruction and sendInstruction to null
     // if empty, to match database constraints

     // Call functions from database.php to create auction
     $result = create_auction($_POST,$db);

     // Check if result code is SUCCES
     if($result->getCode() == 'IMSSP')
     {
       // Redirect to auctions with succes
       header("Location: veilingen.php?succes");
     }
     else
     {
       // Unknown error session
       $_SESSION['warning']['unknown_invalid'] = true;
     }

     $didCreateAuction = true;
     //echo var_dump($result);
   }

   // If auction not succesfull created, remove uploaded images
   if($didCreateAuction === false)
   {
      foreach($_POST['vt_images'] as $row)
      {
        if($row !== NULL)
          unlink($row);
      }
   }
 }
}

$queryPaymentMethods = $db->query("SELECT ID,betalingswijze FROM Betalingswijzen ORDER BY ID ASC");
$queryCountries = $db->query("SELECT lnd_Code,lnd_Landnaam FROM Landen");
// Queries to select paymentMethods and Countries for dropdowns
?>
<!DOCTYPE html>
<html lang="en">
  <head>
      <?php include 'php/includes/default_header.php'; ?>
      <link href="css/veiling.css" rel="stylesheet">
      <title>Verkopen - Eenmaal Andermaal</title>
      <!--
        Textarea editor
      -->
      <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=wlrugg59nxdn2ku32w3x2xbk5mwz4brnjb78npzy9y6xcpm2"></script>
      <script>tinymce.init({ selector:'textarea' });</script>
      <style>
      #mceu_12,#mceu_34,#mceu_47,#mceu_48,#mceu_49,#mceu_50{
        display:none;
      }
      </style>
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
          <div class="row navigation-row fix">
              <h1 style="margin-bottom: 10px" >Veiling starten</h1>
              <p>
                <a href="index.php">
                  <span class="glyphicon glyphicon-home "></span>
                </a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="">Direct regelen</a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="veilingtoevoegen.php">Veiling starten</a>
              </p>
          </div>
          <div class="row content_top_offset">
            <div class="col-lg-12">
            <?php
            // Show notifcations for creating auction
            if(isset($_SESSION['warning']['no_images']) && $_SESSION['warning']['no_images'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">Upload een afbeelding</p>';
            }
            else if(isset($_SESSION['warning']['formaterror']))
            {
              echo '<p class="bg-danger" style="padding:5px;">Het bestand '.$_SESSION['warning']['formaterror'].' heeft een ongeldig bestandsformaat.</p>';
            }
            else if(isset($_SESSION['warning']['filesize']))
            {
              echo '<p class="bg-danger" style="padding:5px;">Het bestand '.$_SESSION['warning']['filesize'].' is te groot.</p>';
            }
            else if(isset($_SESSION['warning']['uploadFailed']))
            {
              echo '<p class="bg-danger" style="padding:5px;">Het bestand '.$_SESSION['warning']['uploadFailed'].' kan door een onbekende fout niet geupload worden.</p>';
            }
            else if(isset($_SESSION['warning']['titel_invalid']) && $_SESSION['warning']['titel_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De titel moet minimaal 2 en maximaal 70 karakters lang zijn en a-Z & 0-9.</p>';
            }
            else if(isset($_SESSION['warning']['description_invalid']) && $_SESSION['warning']['description_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De beschrijving moet minimaal 10 karakters lang zijn.</p>';
            }
            else if(isset($_SESSION['warning']['price_invalid']) && $_SESSION['warning']['price_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven startprijs is ongeldig, de startprijs kan niet negatief of leeg zijn.</p>';
            }
            else if(isset($_SESSION['warning']['rubrieken_invalid']) && $_SESSION['warning']['rubrieken_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">Er zijn geen rubrieken opgegeven, minimaal 1.</p>';
            }
            else if(isset($_SESSION['warning']['paymentinstruction_invalid']) && $_SESSION['warning']['paymentinstruction_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven betalingsinstructie is ongeldig, minimaal 1 en maximaal 30 karakters en a-Z & 0-9.</p>';
            }
            else if(isset($_SESSION['warning']['sendInstruction_invalid']) && $_SESSION['warning']['sendInstruction_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven verzendinstructie is ongeldig, minimaal 1 en maximaal 30 karakters en a-Z & 0-9.</p>';
            }
            else if(isset($_SESSION['warning']['city_invalid']) && $_SESSION['warning']['city_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven plaatsnaam is ongeldig, minimaal 1 en maximaal 30 karakters en a-Z & 0-9.</p>';
            }
            else if(isset($_SESSION['warning']['zipcode_invalid']) && $_SESSION['warning']['zipcode_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven postcode is ongeldig, minimaal 1 en maximaal 9 karakters en a-Z & 0-9.</p>';
            }
            else if(isset($_SESSION['warning']['send_invalid']) && $_SESSION['warning']['send_invalid'] == true)
            {
              echo '<p class="bg-danger" style="padding:5px;">De opgegeven verzendkosten zijn ongeldig, de verzondkosten kunnen niet negatief of leeg zijn. En niet hoger dan &euro;9999,99</p>';
            }
            else if(isset($_SESSION['warning']['unknown_invalid']) && $_SESSION['warning']['unknown_invalid'] == true)
            {
              echo '<p class="bg-warning" style="padding:5px;">Er is een onbekende fout opgetreden</p>';
            }
            ?>
              <i>De velden met een * zijn verplicht</i>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
              <div class="col-lg-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-12">
                        <label for="vt_title">Titel*</label>
                        <input name="vt_title" type="text" class="form-control" id="vt_title" placeholder="Titel" value="<?php if (isset($_POST['vt_title'])){ echo $_POST['vt_title']; }?>">
                        <p></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="form-group">
                          <label for="vt_description">Beschrijving*</label>

                          <textarea class="form-control" rows="10" id="vt_description" name="vt_description" style="max-width:100%;" placeholder="Beschrijving"><?php if (isset($_POST['vt_description'])){ echo $_POST['vt_description']; }?></textarea>
                        </div>
                        <!--
                          1,3,5,7,10
                        -->
                        <div class="row">
                          <div class="col-lg-6">
                            <label for="vt_startPrice">Startprijs*</label>
                            <div class="input-group">
                               <span class="input-group-addon">€</span>
                               <input class="form-control" type="number" id="vt_startPrice" required name="vt_startPrice" value="<?php if (isset($_POST['vt_startPrice'])){ echo $_POST['vt_startPrice']; }else{echo '0.00'; }?>" step="any">
                             </div>
                          </div>
                          <div class="col-lg-6">
                            <label for="vt_auctionTime">Looptijd*</label>
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
                           <label>Rubrieken*</label>
                           <p><i>Tot 2 rubrieken gratis, minimaal 1</i></p>
                           <button type="button" data-toggle="modal" data-target=".bs-example-modal-lg" class="btn btn-orange">Rubriek toevoegen</button>
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
                    <label>Foto's*</label>
                    <p><i>Toegestaande bestandstypen: <?php echo implode(', ',$valid_formats);?></i></p>
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
                        $index = 0;
                        // Show payment Methods in dropdown
                        foreach($queryPaymentMethods as $row)
                        {
                      ?>
                          <option value="<?php echo $row['ID']; ?>" <?php if($index==0){echo 'selected'; }?>><?php echo $row['betalingswijze']; ?></option>
                      <?php
                          $index++;
                        }
                      ?>
                    </select>
                    <p></p>
                    <label for="vt_paymentInstruction">Betalingsinstructie</label>
                    <input name="vt_paymentInstruction" id="vt_paymentInstruction" type="text" class="form-control" placeholder="Betalingsinstructie" value="<?php if (isset($_POST['vt_paymentInstruction'])){ echo $_POST['vt_paymentInstruction']; }?>">
                    <p></p>
                    <label for="vt_city">Plaatsnaam*</label>
                    <input name="vt_city" id="vt_city" type="text" class="form-control" placeholder="Plaatsnaam" value="<?php if (isset($_POST['vt_city'])){ echo $_POST['vt_city']; }?>">
                    <p></p>
                    <label for="vt_zipcode">Postcode*</label>
                    <input name="vt_zipcode" id="vt_zipcode" type="text" class="form-control" placeholder="Postcode" value="<?php if (isset($_POST['vt_zipcode'])){ echo $_POST['vt_zipcode']; }?>">
                    <p></p>
                    <label for="vt_country">Land*</label>
                    <select class="form-control" id="vt_country" name="vt_country">
                      <option disabled selected>Land</p>
                      <?php
                        // Show countries Methods in dropdown
                        foreach($queryCountries as $row)
                        {
                      ?>
                          <option value="<?php echo $row['lnd_Code']; ?>" <?php if($row['lnd_Code'] == 'NL'){ echo 'selected'; } ?>><?php echo $row['lnd_Landnaam']; ?></option>
                      <?php
                        }
                      ?>
                    </select>
                    <p></p>
                    <label for="vt_send">Verzendkosten</label>
                    <div class="input-group">
                       <span class="input-group-addon">€</span>
                       <input class="form-control" type="number" id="vt_send" name="vt_send" value="<?php if (isset($_POST['vt_send'])){ echo $_POST['vt_send']; }else{ echo '0.00'; }?>" step="any">
                    </div>
                    <p></p>
                    <label for="vt_sendInstructions">Verzendinstructie</label>
                    <input name="vt_sendInstructions" id="vt_sendInstructions" type="text" class="form-control" placeholder="Verzendinstructie" value="<?php if (isset($_POST['vt_sendInstructions'])){ echo $_POST['vt_sendInstructions']; }?>">
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
    <!-- Modal to search for rubriek  -->
    <div id="rubriek_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-lg" role="document">
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
              <div id="result_search" style="max-height:65vh;overflow-y:scroll; margin-top:5px;margin-bottom:5px;">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- END Modal to search for rubriek  -->

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

    // search Actions for searching a rubriek

    $( "#searchButton" ).click(function(event){
          var search = $("#rubriekSearchInput").val();
          if(search.length > 0)
            $( "#result_search" ).load( "php/includes/search_rubriek.php?search=" + search);
    });
    $("#rubriekSearchInput").keyup(function(event){
        if(event.keyCode == 13){
            $("#searchButton").click();
        }
    });
    $(document).on("click", '#add_rubriek', function(event) {
        //alert("new link clicked!");
        console.log(this);

        var clickedObject = $(this);

        if ($('#' + clickedObject.data( "rubriekid" )).exists() == false) {

          $('#resultedRubriek').append(
              '<div id="' +
              clickedObject.data( "rubriekid" ) +
              '"><input type="hidden" name="vt_rubrieken[]" value="' +
              clickedObject.data( "rubriekid" ) +
              '" /><p><a class="btn btn-default" id="remove" style="margin-right:10px;"><span class="glyphicon glyphicon-minus"></span></a>' +
              clickedObject.data( "parentparentnaam" ) +
              ' <span class="glyphicon glyphicon-menu-right"></span> ' +
              clickedObject.data( "parentnaam" ) +
              ' <span class="glyphicon glyphicon-menu-right"></span> ' +
              clickedObject.data( "rubrieknaam" ) +
              '</p></div>'
          );

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
    // Functions for loading image live when choosen local
    </script>
  </body>
</html>
<?php
$_SESSION['warning'] = null;
?>
