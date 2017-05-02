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

    <link href="stylesheet.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">
  </head>

  <body>
    <?php include 'php/includes/header.php' ?>

    <div class="container">
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 loginscherm">
          <h1>Registreren</h1>
          <p>Welkom op de beste veilingsite van Nederland</p>

            <div>
            <form class="form-horizontal">



            <div class="login">
                
                  <div class="input-group">
                      <div class="input-group-addon "><span class="glyphicon glyphicon-user" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="text" class="form-control" id="Voornaam" placeholder="Voornaam">
                      <input type="text" class="form-control" id="Achternaam" placeholder="Achternaam">
                      <input type="text" class="form-control datepickerbootstrap" id="Datum" placeholder="Geboortedatum">
                

                  </div>                
                
                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                      <input type="email" class="form-control" id="inputEmail" placeholder="Bevestig email">
                  </div>

                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                      <input type="password" class="form-control" id="inputPassword" placeholder="Wachtwoord">
                    <input type="password" class="form-control" id="inputPassword" placeholder="Bevestig wachtwoord">

                  </div>

                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></div>
                      <input style="max-width:60%" type="adres" class="form-control" id="Adres" placeholder="Adres">
                      <input style="max-width:40%" type="Number" class="form-control" id="Nummer" placeholder="Nummer">
                      <input type="" class="form-control" id="Postcode" placeholder="Postcode" pattern="[1-9][0-9]{3}\s?[a-zA-Z]{2}">
                  </div>
               




            </div>

              <div class="bevestig">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="position:relative;">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <button type="submit" class="btn btn-orange align-right" >Registreer</button>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                </div>
              </div>
            </form>
            </div>
            </div>
      </div>

    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script>
    /*
    $("li.toggle-sub").click(function(evt) {

      evt.preventDefault();
      $(this).children("span").toggleClass('glyphicon-menu-right');
      $(this).children("span").toggleClass('glyphicon-menu-down');
      $(this).children(".sub").toggle();
    });
    */
    $('.datepickerbootstrap').datepicker({
    });
    </script>
  </body>
</html>
