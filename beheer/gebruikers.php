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
    //include '../php/includes/header.php';            
    require_once('../php/database.php'); 
    pdo_connect();

    $sorteerOp = "gebruikersnaam";
    $pagina = 0;
    $selectVoornaam = false;
    $selectAchternaam = false;
    $selectGebruikersnaam = false;
    $selectGeboortedatum = false;
    $selectBeoordeling = false;
    $selectStatus = false;
    $selectAccountType = false;
    $selectEmail = false;
    $selectAdresregel1 = false;
    $selectAdresregel2 = false;
    $selectPostcode = false;
    $selectPlaatsnaam = false;
    $selectLand = false;




    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!empty($_GET)) {
          if(!empty($_GET['sorteerOp'])){
            $sorteerOp = htmlspecialchars($_GET['sorteerOp']); 
          }else{
            $sorteerOp='';
          }
          if(!empty($_GET['pagina'])){
            $pagina = htmlspecialchars($_GET['pagina']);  
          } else {
            $pagina = 0;
          }
          if(!empty($_GET['selectVoornaam'])){
            $selectVoornaam = ($_GET['selectVoornaam'] === 'true');
          } else {
            $selectVoornaam = false;
          }
          if(!empty($_GET['selectAchternaam'])){
            $selectAchternaam= ($_GET['selectAchternaam'] === 'true');
          }else{
            $selectAchternaam = false;
          }
          if(!empty($_GET['selectGebruikersnaam'])){
            $selectGebruikersnaam = ($_GET['selectGebruikersnaam'] === 'true');
          }else{
            $selectGebruikersnaam = false;
          }
          if(!empty($_GET['selectAdresregel1'])){
            $selectAdresregel1 = ($_GET['selectAdresregel1'] === 'true');
          }else{
            $selectAdresregel1 = false;
          }
          if(!empty($_GET['selectAdresregel2'])){
            $selectAdresregel2 = ($_GET['selectAdresregel2'] === 'true');
          }else{
            $selectAdresregel2 = false;
          }
          if(!empty($_GET['selectPostcode'])){
            $selectPostcode = ($_GET['selectPostcode'] === 'true');
          }else{
            $selectPostcode = false;
          }
          if(!empty($_GET['selectPlaatsnaam'])){
            $selectPlaatsnaam = ($_GET['selectPlaatsnaam'] === 'true');
          }else{
            $selectPlaatsnaam = false;
          }
          if(!empty($_GET['selectLand'])){
            $selectLand = ($_GET['selectLand'] === 'true');
          }else{
            $selectLand = false;
          }
          if(!empty($_GET['selectGeboortedatum'])){
            $selectGeboortedatum = ($_GET['selectGeboortedatum'] === 'true');
          }else{
            $selectGeboortedatum = false;
          }
          if(!empty($_GET['selectEmail'])){
            $selectEmail = ($_GET['selectEmail'] === 'true');
          }else{
            $selectEmail = false;
          }
          if(!empty($_GET['selectAccountType'])){
            $selectAccountType = ($_GET['selectAccountType'] === 'true');
          }else{
            $selectAccountType = false;
          }
          if(!empty($_GET['selectBeoordeling'])){
            $selectBeoordeling = ($_GET['selectBeoordeling'] === 'true');
          }else{
            $selectBeoordeling = false;
          }
          if(!empty($_GET['selectStatus'])){
            $selectStatus = ($_GET['selectStatus'] === 'true');
          }else{
            $selectStatus = false;
          }
      }
    }
    ?>

    <main class="container">
    <h1> Beheer </h1>
        <section class="row">
    <aside class="col-md-4 col-lg-2 col-sm-4 sidebar">
      <h3>Selectie</h3>
  <form action="gebruikers.php" method="get" class="form-check" >
    <h4>Soort </h4>
    <input type="radio" name="user-type" value="Buyer" checked> Kopers<br>
    <input type="radio" name="user-type" value="Seller"> Verkopers<br>
    <input type="radio" name="user-type" value="Both"> Alle

    <h4>Kolommen </h4>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectVoornaam" value="true" class="form-check-input"
        <?php if($selectVoornaam){ echo 'checked';} ?> >
        Voornaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAchternaam" value="true" class="form-check-input"
        <?php if($selectAchternaam){ echo 'checked';} ?> >
        Achternaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectGebruikersnaam" value="true" class="form-check-input"
        <?php if($selectGebruikersnaam){ echo 'checked';} ?> >
        Gebruikersnaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectEmail" value="true" class="form-check-input" 
        <?php if($selectEmail){ echo 'checked';} ?> >
        Email
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectGeboortedatum" value="true" class="form-check-input"
        <?php if($selectGeboortedatum){ echo 'checked';} ?> >
        Geboortedatum
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectBeoordeling" value="true" class="form-check-input" 
        <?php if($selectBeoordeling){ echo 'checked';} ?> >
        Beoordeling
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectStatus" value="true" class="form-check-input" 
        <?php if($selectStatus){ echo 'checked';} ?> >
        Status
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectPostcode" value="true" class="form-check-input" 
        <?php if($selectPostcode){ echo 'checked';} ?> >
        Postcode
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAdresregel1" value="true" class="form-check-input" 
        <?php if($selectAdresregel1){ echo 'checked';} ?> >
        Adres regel 1
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAdresregel2" value="true" class="form-check-input" 
        <?php if($selectAdresregel2){ echo 'checked';} ?> >
        Adres regel 2
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectPlaatsnaam" value="true" class="form-check-input" 
        <?php if($selectPlaatsnaam){ echo 'checked';} ?> >
        Plaatsnaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectLand" value="true" class="form-check-input" 
        <?php if($selectLand){ echo 'checked';} ?> >
        Land
      </label>
      <button class="btn btn-orange" type="submit"  name="Accept" >
      <i class="glyphicon glyphicon-ok"></i>
      Pas toe
      </button>
  </form>
    </aside>
            <article class="col-md-8">
                <h2> Gebruikers </h2>
                <table class="table table-hover">
                    <thead>
                    <tr>
                    <?php 
                    if($selectVoornaam){ echo '<th>Voornaam</th>';}
                    if($selectAchternaam){ echo '<th>Achternaam</th>';}
                    if($selectGebruikersnaam){ echo '<th>Gebruikersnaam</th>';}
                    if($selectEmail){ echo '<th>Email</th>';}
                    if($selectAccountType){ echo '<th>Account type</th>';}
                    if($selectBeoordeling){ echo '<th>Beoordeling</th>';}
                    if($selectStatus){ echo '<th>Status</th>';}
                    if($selectLand){ echo '<th>Land</th>';}
                    if($selectPlaatsnaam){ echo '<th>Plaatsnaam</th>';}
                    if($selectPostcode){ echo '<th>Postcode</th>';}
                    if($selectAdresregel1){ echo '<th>Adres regel 1</th>';}
                    if($selectAdresregel2){ echo '<th>Adres regel 2</th>';}
                    ?>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $data = $db->query("SELECT gebruikersnaam, voornaam, achternaam, Accountstatussen.omschrijving AS status 
                                            FROM Gebruikers
                                            INNER JOIN Accountstatussen 
                                              ON Gebruikers.statusID=Accountstatussen.ID");

                        while ($row = $data->fetch()){
                          echo "<td>$gebruikersnaam</td>";
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
