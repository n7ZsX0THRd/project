<?php
include('../php/database.php');
pdo_connect();
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET)) {
      $gebruikersnaam = htmlspecialchars($_GET['gebruikersnaam']);
    }
    else {
      $gebruikersnaam = 'gebruiker niet gevonden';
    }
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
    <link href="../bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../bootstrap/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../bootstrap/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="../stylesheet.css" rel="stylesheet">
    <link href="../css/profilestyle.css" rel="stylesheet">
  </head>

  <body>

    <?php include '../php/includes/header.php' ?>

    <main class="container">
        <h1> Beheer gebruiker</h1>
        <section class="row profile">
            <article class="col-md-3">
                <aside class="profile-sidebar">
                    <div class="user">
                        <h2><?php echo $gebruikersnaam ?></h2>
                        <img class="img-responsive img-circle" src="../images/users/JohnDoe.jpg" alt="John Doe face">
                        <button class="btn btn-niagara" type="button" name="Bericht" >
                            <i class="glyphicon glyphicon-envelope"></i>
                            Bericht
                        </button>
                        <div class="btn-group" data-toggle="buttons">
                            <button class="btn btn-orange" type="button" name="Bewerken" >
                                <i class="glyphicon glyphicon-edit"></i>
                                Bewerken
                            </button>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#myModal" >
                                <i class="glyphicon glyphicon-trash"></i>
                                Verwijder
                            </button>
                        </div>
                    </div>

                    <div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
                      <div class="modal-dialog modal-sm" role="document">
                         <div class="modal-content">
                           <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                             <h4 class="modal-title" id="myModalLabel">Weet u zeker dat u deze gebruiker wil verwijderen?</h4>
                           </div>
                           <div class="modal-footer">
                             <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                             <button type="button" class="btn btn-primary" onclick="myAjax(['delete','<?php echo $gebruikersnaam ?>'])">Verwijderen</button>
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
                </div>
		</article>

        </section>
    </main>



    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>function myAjax(actionvar) {
          $.ajax({
               type: "POST",
               url: 'ajax.php',
               data:{action:actionvar},
               success:function(html) {
                 window.location.href = "gebruikers.php";
                 alert(html);
               }
          });
     } </script>
  </body>
</html>
