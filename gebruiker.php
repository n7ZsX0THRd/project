<?php
/*
  iProject Groep 2
  30-05-2017

  file: gebruiker.php
  purpose:
  Page for administrator to view info of a specific user.
*/
  session_start();
  $_SESSION['menu']['sub'] = 'bp';
  include_once ('php/database.php');
  include_once ('php/user.php');
  pdo_connect();
  // Include database, and include user functions.
  // Connect to database

  if(isUserBeheerder($db) == false){
    header("Location: index.php");
  }
  // If user is not an administrator redirect to homepage

  // Get administrator details
  $result = getLoggedInUser($db);


  // If Request method == GET load user data
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!empty($_GET)) {
        $gebruikersnaam = htmlspecialchars($_GET['gebruikersnaam']);

        $data = $db->prepare("SELECT
        gebruikersnaam,
        voornaam,
        achternaam,
        adresregel1,
        adresregel2,
        postcode,
        plaatsnaam,
        land,
        geboortedatum,
        emailadres,
        typegebruiker,
        statusID,
        datepart(day,[geboortedatum]) AS geboortedag,
        datepart(month,[geboortedatum]) AS geboortemaand,
        datepart(year,[geboortedatum]) AS geboortejaar,
        biografie,
        bestandsnaam FROM Gebruikers WHERE gebruikersnaam=?");
        $data->execute([$gebruikersnaam]);
        $data2 = $db->prepare("SELECT * FROM Gebruikerstelefoon WHERE gebruikersnaam=?");
        $data2->execute([$gebruikersnaam]);

        $resultUser=$data->fetchAll();
        $resultUserPhone=$data2->fetchAll();

        // If user is not found redirect back to gebruikers.php
        if(count($resultUser) === 0){
          header("Location: gebruikers.php"); // Username ongeldig
        }


        // Check if user is blocked
        if ($resultUser[0]['statusID']==3){
            $blocked = true;
        } else {
            $blocked = false;
        }

        // Check if the user wants to become a verkoper
        if ($resultUser[0]['typegebruiker']==4){
            $brief = true;
        } else {
            $brief = false;
        }

        // Check if an user has an image if image not found or empty, show default user image
        if(!empty($resultUser[0]['bestandsnaam'])) {
          $image = $resultUser[0]['bestandsnaam'];
          $file = 'images/users/'.$image;
          if (!file_exists( $file )){
            $image = "geenfoto/geenfoto.png";
          }
        }
        else {
          $image = "geenfoto/geenfoto.png";
        }
      }
      else {
          // Geen username opgegeven, redirect gebruikers.php
          header("Location: gebruikers.php");
          // Get empty
      }
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
      include 'php/includes/default_header.php';
      // Include default page head
    ?>
    <link href="css/profilestyle.css" rel="stylesheet">
    <title>Beheerpagina - EenmaalAndermaal</title>
  </head>

  <body>

    <?php
      include 'php/includes/header.php';
      // Include page navigation header
    ?>

    <div class="container">
        <div class="row">
          <div class="col-md-3 col-lg-2 col-sm-4 sidebar">
            <?php
            include 'php/includes/sidebar.php';
            // Include website sidebar
            ?>
          </div>
          <div class="col-md-9 col-lg-10 col-sm-8">
            <div class="container-fluid  content_col">
              <!-- Breadcrumb -->
              <div class="row navigation-row">
                  <p>
                    <a href="index.php">
                      <span class="glyphicon glyphicon-home "></span>
                    </a>
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <a href="">Beheerpanel</a>
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Gebruiker</a>
                  </p>
              </div>
              <!-- Breadcrumb END -->
              <div class="row">
                <h1> Beheer gebruiker: <?php echo $gebruikersnaam ?></h1>
              </div>
              <div class="row content_top_offset">
                <div class="col-lg-6" style="border-right:1px solid #e7e7e7;">
                    <!-- Voornaam en achternaam -->
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Voornaam</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['voornaam']?>
                            </div>
                        </div>
                        <div class="col-lg-8">
                          <label for="exampleInputEmail1">Achternaam</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['achternaam']?>
                            </div>
                        </div>
                      </div>
                    </div>

                    <!-- geboortedatum -->
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Dag</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['geboortedag']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Maand</label>
                            <?php
                            $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");?>
                              <div class="pflijn">
                                    <?php echo $months[$resultUser[0]['geboortemaand']-1];?>
                              </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Jaar</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['geboortejaar'];?>
                            </div>
                        </div>
                      </div>
                    </div>

                    <!-- adresgegevens -->
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-8">
                          <label for="exampleInputEmail1">Adres + Huisnr.</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['adresregel1']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Postcode</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['postcode']?>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-12">
                          <label for="exampleInputEmail1">Adresregel 2</label>
                            <div class="pflijn">
                                <?php
                                  if(isset($resultUser[0]['adresregel2']))
                                  {
                                    echo $resultUser[0]['adresregel2'];
                                  }
                                  else {
                                    echo '<br>';
                                  }
                                ?>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-8">
                          <label for="exampleInputEmail1">Woonplaats</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['plaatsnaam']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Landcode</label>
                            <div class="pflijn">
                                <?php echo $resultUser[0]['land']?>
                            </div>
                        </div>
                      </div>
                    </div>

                    <!-- Telefoonnummer -->
                    <div class="form-group">
                      <label for="tel">Telefoonnummer</label>
                      <div class="pflijn">
                          <?php
                            echo $resultUserPhone[0]['telefoonnummer'];
                          ?>
                      </div>
                    </div>

                </div>
                <div class="col-lg-6">

                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-7">
                          <label for="exampleInputEmail1">Gebruikersnaam</label>
                          <div class="pflijn">
                              <?php echo $resultUser[0]['gebruikersnaam']?>
                          </div>
                          <!-- email -->
                          <p></p>
                          <label for="exampleInputEmail1">Email</label>
                          <div class="pflijn">
                              <?php echo $resultUser[0]['emailadres']?>
                          </div>
                        </div>
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg-4">
                            <div class="profile_picture" style="background-image:url(images/users/<?php echo $image; ?>);">
                            </div>
                        </div>

                      </div>

                      <div class="row">
                        <div class="col-lg-12">
                           <div class="form-group">
                            <label for="exampleInputFile">Biografie</label>
                            <div class="pflijn biografietext">
                                <?php
                                  echo $resultUser[0]['biografie'];
                                ?>
                            </div>
                           </div>
                        </div>
                      </div>
                    </div>

                </div>

              </div>
              <div class="row">
                <div class="col-lg-12">
                  <hr>
                </div>
                <div class="col-lg-6 col-lg-offset-6">

                  <div class="text-right">
                    <div class="profielbutton-group">
                    <?php
                        // 
                        if ($brief){
                          $gebruikersnaam=$resultUser[0]['gebruikersnaam'];
                          $data = $db->prepare("  
                                                SELECT TOP (1) startdatum 
                                                FROM Verkopers
                                                WHERE gebruikersnaam= ?; 
                                              ");
                                  
                          $data->execute(array($gebruikersnaam));
                          $result=$data->fetchAll();
                          if (count($result)==1 &&  isset($result[0]['startdatum'])){
                            echo 'Laatste brief aangemaakt op: ';
                            echo $result[0]['startdatum'];
                          }

                          ?>
                          <form action="post.php" method="POST">
                            <input type="hidden" name="voornaam" value="<?php echo $resultUser[0]['voornaam'] ?>">
                            <input type="hidden" name="achternaam" value="<?php echo $resultUser[0]['achternaam'] ?>">
                            <input type="hidden" name="gebruikersnaam" value="<?php echo $resultUser[0]['gebruikersnaam'] ?>">
                            <input type="hidden" name="adresregel" value="<?php echo $resultUser[0]['adresregel1'] ?>">
                            <input type="hidden" name="postcode" value="<?php echo $resultUser[0]['postcode'] ?>">
                            <input type="hidden" name="plaats" value="<?php echo $resultUser[0]['plaatsnaam'] ?>">
                            <input type="hidden" name="activatiecode" value="<?php echo rand(100000, 999999) ?>">

                            <button type="submit" name=Submit class="btn btn-niagara"  >
                                <i class="glyphicon glyphicon-envelope"></i>
                                Activatie brief
                            </button>
                          </form>
                            <?php
                        }  ?>
                      <?php
                        // If user is blocked show deblock button otherwise show block button
                        if ($blocked){ ?>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#myModalDeBlock" >
                              <i class="glyphicon glyphicon-ban-circle"></i>
                              Deblokkeer
                          </button>
                            <?php
                        } else { ?>
                          <button class="btn btn-danger" data-toggle="modal" data-target="#myModalBlock" >
                              <i class="glyphicon glyphicon-ban-circle"></i>
                              Blokkeer
                          </button>
                          <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

    </div>
    <!-- CONTAINER END -->

    <!-- DeBlock user modal -->
    <div class="modal fade bs-example-modal-sm" id="myModalBlock" tabindex="-1" role="dialog" aria-labelledby="myModalBlock">
      <div class="modal-dialog modal-sm" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
             <h4 class="modal-title" id="myModalLabel">Weet u zeker dat u deze gebruiker wilt blokkeren?</h4>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
             <button type="button" class="btn btn-orange" onclick="myAjax(['block','<?php echo $gebruikersnaam ?>'])">Blokkeren</button>
           </div>
         </div>
       </div>
    </div>
    <!-- DeBlock user modal END-->

    <!-- Block user modal -->
    <div class="modal fade bs-example-modal-sm" id="myModalDeBlock" tabindex="-1" role="dialog" aria-labelledby="myModalBlock">
      <div class="modal-dialog modal-sm" role="document">
         <div class="modal-content">
           <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
             <h4 class="modal-title" id="myModalLabel">Weet u zeker dat u deze gebruiker wilt deblokkeren?</h4>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
             <button type="button" class="btn btn-orange" onclick="myAjax(['unBlock','<?php echo $gebruikersnaam ?>'])">Deblokkeren</button>
           </div>
         </div>
       </div>
    </div>
    <!-- Block user modal END-->
   <?php
    include 'php/includes/footer.php';
    // Include page footer
   ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>function myAjax(actionvar) {
          $.ajax({
               type: "POST",
               url: 'ajax.php',
               data:{action:actionvar},
               success:function(html) {
               location.reload();
               }
          });
     } </script>
  </body>
</html>
