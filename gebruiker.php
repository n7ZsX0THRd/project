<?php
  session_start();
  include ('php/database.php');
  include ('php/user.php');
  pdo_connect();

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!empty($_GET)) {
        $gebruikersnaam = htmlspecialchars($_GET['gebruikersnaam']);

        $data = $db->prepare("SELECT * FROM gebruikers WHERE gebruikersnaam=?");
        $data->execute([$gebruikersnaam]);
        $result=$data->fetchAll();
        if ($result[0]['statusID']==3){
            $blocked = true;
        } else {
            $blocked = false;
        }

        if(!empty($result['bestandsnaam'])) {
          $image = $result['bestandsnaam'];
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
        <h1> Beheer gebruiker</h1>
        <section class="row profile">
            <article class="col-md-3">
                <aside class="profile-sidebar">
                    <div class="user">
                        <h2><?php echo $gebruikersnaam ?></h2>
                        <img class="img-responsive img-circle" src= "images/users/<?php echo $image ?>" alt="Profile picture">
                        <div class="profielbutton-group">
                          <div class="btn-group" data-toggle="buttons">
                          <?php
                            if ($blocked){ ?>
                                <button class="btn btn-danger" data-toggle="modal" data-target="#myModalDeBlock" >
                                  <i class="glyphicon glyphicon-ban-circle"></i>
                                  de-Blokkeer
                              </button>
                                <?php
                            } else { ?>
                              <button class="btn btn-danger" data-toggle="modal" data-target="#myModalBlock" >
                                  <i class="glyphicon glyphicon-ban-circle"></i>
                                  Blokkeer
                              </button>
                              <?php } ?>
                              <button class="btn btn-niagara" data-toggle="modal" data-target="#myModalSendMessage" >
                                  <i class="glyphicon glyphicon-envelope"></i>
                                  Stuur bericht
                              </button>
                          </div>
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

                    <div class="profile-usermenu">
                        <ul class="nav">
                            <li class="active">
                                <a href="#">
                                <i class="glyphicon glyphicon-home"></i>
                                Overzicht </a>
                            </li>
                            <li>
                                <a href="#">
                                <i class="glyphicon glyphicon-user"></i>
                                Instellingen</a>
                            </li>
                            <li>
                                <a href="#" target="_blank">
                                <i class="glyphicon glyphicon-eur"></i>
                                Betalingen </a>
                            </li>
                            <li>
                                <a href="#" target="_blank">
                                <i class="glyphicon glyphicon-star"></i>
                                Beoordelingen </a>
                            </li>
                        </ul>
                    </div>

                </aside>
            </article>

            <article class="col-md-9">
                <div class="user-content">
                    <h2>Over <?php echo $gebruikersnaam ?></h2>
                    <?php

                    if (isset($result[0]["biografie"])){
                         $biografie = $result[0]["biografie"];
                    } else {
                        $biografie = "heeft geen backstory";
                    }
                    echo $biografie;


                     ?>

                </div>
		</article>

        </section>
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
