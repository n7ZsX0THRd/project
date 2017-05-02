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
  </head>

  <body>
 
    <?php include '../php/includes/header.php' ?>


    <main class="container">
        <section class="row"> 
            <h1> Beheer </h1>
            <article class="col-md-8">
                <h2> Kopers </h2>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th># ID</td>
                        <th>Voornaam</th>
                        <th>Achternaam</th>
                        <th>Email</th>
                        <th>Beoordeling</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr onclick="document.location = 'koper.php';">
                        <td>1234</td>
                        <td>John</td>
                        <td>Doe</td>
                        <td>john@example.com</td>
                        <td>95.7 %</td>
                        <td>Actief</td>
                    </tr>
                    <tr onclick="document.location = 'koper.php';">
                        <td>1337</td>
                        <td>Mary</td>
                        <td>Moe</td>
                        <td>mary@example.com</td>
                        <td>81.3 %</td>
                        <td>In-actief</td>
                    </tr>
                    <a href="http://example.com">
                    <tr onclick="document.location = 'koper.php';">
                        <td>2417</td>
                        <td>July</td>
                        <td>Dooley</td>
                        <td>july@example.com</td>
                        <td>98.8 %</td>
                        <td>Actief</td>
                    </tr>
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
