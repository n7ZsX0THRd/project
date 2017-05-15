<?php
  session_start();
  include ('php/database.php');
  include ('php/user.php');
  pdo_connect();

  if(isUserBeheerder($db) == false){
    header("Location: index.php");
  }


  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!empty($_GET)) {
        $gebruikersnaam = htmlspecialchars($_GET['gebruikersnaam']);

        $data = $db->prepare("SELECT
        gebruikersnaam,
        voornaam,
        achternaam,
        adresregel1,
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

        $result=$data->fetchAll();
        $result2=$data2->fetchAll();

        if ($result[0]['statusID']==3){
            $blocked = true;
        } else {
            $blocked = false;
        }

        if(!empty($result[0]['bestandsnaam'])) {
          $image = $result[0]['bestandsnaam'];
          $file = 'images/users/'.$image;
          if (!file_exists( $file )){
            $image = '404.png';
          }
        }
        else {
          $image = "geenfoto/geenfoto.png";
        }
      }
      else {
        $gebruikersnaam = 'gebruiker niet gevonden';
        $image = "geenfoto/geenfoto.png";
      }
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST'){
      send_message($_POST);
  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../bootstrap/favicon.ico">

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
    <link href="css/profilestyle.css" rel="stylesheet">
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>

    <main class="container">
        <h1> Beheer gebruiker: <?php echo $gebruikersnaam ?></h1>
        <section class="row profile">
            <article class="col-md-3">
                <aside class="profile-sidebar">
                    <div class="user">
                        <div class="profielbutton-group">
                          <?php
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
                              <button class="btn btn-niagara" disabled data-toggle="modal" data-target="#myModalSendMessage" >
                                  <i class="glyphicon glyphicon-envelope"></i>
                                  Stuur bericht
                              </button>
                        </div>
                    </div>
                    <div class="modal fade bs-example-modal-lg" id="myModalSendMessage" tabindex="-1" role="dialog" aria-labelledby="myModalSendMessage">
                      <div class="modal-dialog modal-lg" role="document">
                         <div class="modal-content">
                           <div class="modal-header">
                             <h4 class="modal-title">
                               Bericht aan <?php echo $gebruikersnaam ?>
                             </h4>
                           </div>
                           <form method="post" target="gebruiker.php">
                             <div class="modal-footer">
                               <div class="row">
                                 <div class="col-lg-12">
                                   <div class="form-group">
                                     <textarea class="form-control" rows="10" style="max-width:100%" name="g_message"  maxlength="255">

                                     </textarea>
                                   </div>
                                 </div>
                               </div>
                               <button type="submit" data-dismiss="modal" class="btn btn-orange" >Verzenden</button>
                             </div>

                           </form>
                         </div>
                       </div>
                    </div>
                    </div>
                    <div class="modal fade bs-example-modal-sm" id="myModalBlock" tabindex="-1" role="dialog" aria-labelledby="myModalBlock">
                      <div class="modal-dialog modal-sm" role="document">
                         <div class="modal-content">
                           <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Weet u zeker dat u deze gebruiker wil blokkeren?</h4>
                           </div>
                           <div class="modal-footer">
                             <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                             <button type="button" class="btn btn-primary" onclick="myAjax(['block','<?php echo $gebruikersnaam ?>'])">Blokkeren</button>
                           </div>
                         </div>
                       </div>
                    </div>

                    <div class="modal fade bs-example-modal-sm" id="myModalDeBlock" tabindex="-1" role="dialog" aria-labelledby="myModalBlock">
                      <div class="modal-dialog modal-sm" role="document">
                         <div class="modal-content">
                           <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Weet u zeker dat u deze gebruiker wil deblokkeren?</h4>
                           </div>
                           <div class="modal-footer">
                             <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                             <button type="button" class="btn btn-primary" onclick="myAjax(['unBlock','<?php echo $gebruikersnaam ?>'])">Deblokkeren</button>
                           </div>
                         </div>
                       </div>
                    </div>
                    <div class="container">
                      <div class="row">
                        <div class="col-md-4 col-lg-2 col-sm-4 sidebar">
                          <h3>Gebruiker</h3>
                          <ul class="menubar">
                            <li class="toggle-sub">
                              <a href="">Direct regelen</a>
                            </li>
                            <ul class="sub">
                              <li>
                                <a href="">Laatste bieding</a>
                              </li>
                              <li>
                                <a href="">Verkopen</a>
                              </li>
                            </ul>
                            <li class="toggle-sub">
                              <a href="">Mijn Account</a>
                            </li>
                            <ul class="sub">
                              <li>
                                <a href="">Mijn biedingen</a>
                              </li>
                              <li>
                                <a href="">Mijn favorieten</a>
                              </li>
                              <li>
                                <a href="">Instellingen</a>
                              </li>
                              <li>
                                <a href="php/logout.php">Log uit</a>
                              </li>
                            </ul>
                            <?php if($result[0]['typegebruiker'] ==3){?>
                            <li class="toggle-sub">
                              <a href="gebruikers.php">Beheerpanel</a>
                            </li>
                            <?php } ?>
                          </ul>
                        </div>
                     </div>
                  </div>
                </aside>
            </article>

            <article class="col-md-9">
              <div class="row content_top_offset">
                <div class="row navigation-row">
                    <p>
                      <a href="">
                        <span class="glyphicon glyphicon-home "></span>
                      </a>
                      <span class="glyphicon glyphicon-menu-right"></span>
                      <a href="">Mijn Account</a>
                      <span class="glyphicon glyphicon-menu-right"></span>
                      <a href="">Instellingen</a>
                    </p>
                </div>

                <div class="col-lg-6" style="border-right:1px solid #e7e7e7;">
                  <form method="post" enctype="multipart/form-data" action="">
                    <input type="hidden" name="form_name" value="changeprofile"/>
                    <!-- email -->
                    <div class="form-group">
                      <label for="exampleInputEmail1">Email</label>
                        <div class="pflijn">
                            <?php echo $result[0]['emailadres']?>
                        </div>
                    </div>

                    <!-- Voornaam en achternaam -->
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Voornaam</label>
                            <div class="pflijn">
                                <?php echo $result[0]['voornaam']?>
                            </div>
                        </div>
                        <div class="col-lg-8">
                          <label for="exampleInputEmail1">Achternaam</label>
                            <div class="pflijn">
                                <?php echo $result[0]['achternaam']?>
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
                                <?php echo $result[0]['geboortedag']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Maand</label>
                            <?php
                            $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");?>
                              <div class="pflijn">
                                    <?php echo $months[$result[0]['geboortemaand']-1];?>
                              </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Jaar</label>
                            <div class="pflijn">
                                <?php echo $result[0]['geboortejaar'];?>
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
                                <?php echo $result[0]['adresregel1']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Postcode</label>
                            <div class="pflijn">
                                <?php echo $result[0]['postcode']?>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-8">
                          <label for="exampleInputEmail1">Woonplaats</label>
                            <div class="pflijn">
                                <?php echo $result[0]['plaatsnaam']?>
                            </div>
                        </div>
                        <div class="col-lg-4">
                          <label for="exampleInputEmail1">Landcode</label>
                            <div class="pflijn">
                                <?php echo $result[0]['land']?>
                            </div>
                        </div>
                      </div>
                    </div>

                    <!-- Telefoonnummer -->
                    <div class="form-group">
                      <label for="tel">Telefoonnummer</label>
                      <div class="pflijn">
                          <?php
                            echo $result2[0]['telefoonnummer'];
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
                              <?php echo $result[0]['gebruikersnaam']?>
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
                                  echo $result[0]['biografie'];
                                ?>
                            </div>
                           </div>
                        </div>
                      </div>
                    </div>

                </div>

              </div>
		</article>

        </section>
        <?php include 'php/includes/footer.php' ?>

    </main>



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
