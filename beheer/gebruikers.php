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

    <title>Gebruikers</title>

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
    <link href="../css/dashboard.css" rel="stylesheet">
  </head>

  <body>
 
    <?php 
    include '../php/includes/header.php';            
    require_once('../php/database.php'); 
    pdo_connect();
    
    ?>

    <main class="container">
    <h1> Beheer </h1>
        <section class="row">
    <aside class="col-md-4 col-lg-2 col-sm-4 sidebar">
      <h3>Selectie</h3>
  <div class="form-check" >
  <h4>Soort </h4>
  <input type="radio" name="user-type" value="Buyer" checked> Kopers<br>
  <input type="radio" name="user-type" value="Seller"> Verkopers<br>
  <input type="radio" name="user-type" value="Both"> Alle

  <h4>Kolommen </h4>
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input" checked>
      ID
    </label>
    <label class="form-check-label selectie">
      <input type="checkbox" class="form-check-input" checked>
      Voornaam
    </label>
    <label class="form-check-label selectie">
      <input type="checkbox" class="form-check-input" checked>
      Achternaam
    </label>
    <label class="form-check-label selectie">
      <input type="checkbox" class="form-check-input" checked>
      Email
    </label>
    <label class="form-check-label selectie">
      <input type="checkbox" class="form-check-input" checked>
      Beoordeling
    </label>
    <label class="form-check-label selectie">
      <input type="checkbox" class="form-check-input" checked>
      Status
    </label>
    <button class="btn btn-orange" type="button" name="Accept" >
    <i class="glyphicon glyphicon-ok"></i>
    Pas toe
    </button>
  </div>
    </aside>
            <article class="col-md-8">
                <h2> Gebruikers </h2>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Gebruikersnaam</td>
                        <th>Voornaam</th>
                        <th>Achternaam</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $data = $db->query("SELECT gebruikersnaam, voornaam, achternaam, Accountstatussen.omschrijving AS status FROM Gebruiker 
                                            INNER JOIN Accountstatussen 
                                              ON Gebruiker.statusID=Accountstatussen.ID");

                        while ($row = $data->fetch()){
                          echo "<tr onclick='document.location = 'koper.php';'>"; //fix this
                          echo "<td>$row[gebruikersnaam]</td>";
                          echo "<td>$row[voornaam]</td>";
                          echo "<td>$row[achternaam]</td>";
                          echo "<td>$row[status]</td>";
                          echo "</tr>";
                        }
                      ?>
                    </tbody>
                </table>
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
  </body>
</html>
