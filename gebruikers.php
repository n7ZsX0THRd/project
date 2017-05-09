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

    <title>Gebruikers</title>

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
    <link href="css/dashboard.css" rel="stylesheet">
  </head>

  <body>
 
    <?php 
    //include '../php/includes/header.php';            
    require_once('php/database.php'); 
    pdo_connect();

  
    $selectie = array(
      "sorteerOp" => "Achternaam",
      "pagina" => 0,
      "selectVoornaam" => false,
      "selectAchternaam" => false,
      "selectGebruikersnaam" => false,
      "selectGeboortedatum" => false,
      "selectBeoordeling" => false,
      "selectStatus" => false,
      "selectAccountType" => false,
      "selectEmail" => false,
      "selectAdresregel1" => false,
      "selectAdresregel2" => false,
      "selectPostcode" => false,
      "selectPlaatsnaam" => false,
      "selectLand" => false
    );

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      if (!empty($_GET)) {
          if(!empty($_GET['sorteerOp'])){
            $selectie["sorteerOp"] = htmlspecialchars($_GET['sorteerOp']); 
          }else{
            $selectie["sorteerOp"] = 'Achternaam';
          }
          if(!empty($_GET['pagina'])){
            $selectie["pagina"] = htmlspecialchars($_GET['pagina']);  
          } else {
            $selectie["pagina"] = 0;
          }
          if(!empty($_GET['selectVoornaam'])){
            $selectie["selectVoornaam"] = ($_GET['selectVoornaam'] === 'true');
          } else {
            $selectie["selectVoornaam"] = false;
          }
          if(!empty($_GET['selectAchternaam'])){
            $selectie["selectAchternaam"] = ($_GET['selectAchternaam'] === 'true');
          }else{
            $selectie["selectAchternaam"] = false;
          }
          if(!empty($_GET['selectGebruikersnaam'])){
            $selectie["selectGebruikersnaam"] = ($_GET['selectGebruikersnaam'] === 'true');
          }else{
            $selectie["selectGebruikersnaam"] = false;
          }
          if(!empty($_GET['selectAdresregel1'])){
            $selectie["selectAdresregel1"] = ($_GET['selectAdresregel1'] === 'true');
          }else{
            $selectie["selectAdresregel1"] = false;
          }
          if(!empty($_GET['selectAdresregel2'])){
            $selectie["selectAdresregel2"] = ($_GET['selectAdresregel2'] === 'true');
          }else{
            $selectie["selectAdresregel2"] = false;
          }
          if(!empty($_GET['selectPostcode'])){
            $selectie["selectPostcode"] = ($_GET['selectPostcode'] === 'true');
          }else{
            $selectie["selectPostcode"] = false;
          }
          if(!empty($_GET['selectPlaatsnaam'])){
            $selectie["selectPlaatsnaam"] = ($_GET['selectPlaatsnaam'] === 'true');
          }else{
            $selectie["selectPlaatsnaam"] = false;
          }
          if(!empty($_GET['selectLand'])){
            $selectie["selectLand"] = ($_GET['selectLand'] === 'true');
          }else{
            $selectie["selectLand"] = false;
          }
          if(!empty($_GET['selectGeboortedatum'])){
            $selectie["selectGeboortedatum"] = ($_GET['selectGeboortedatum'] === 'true');
          }else{
            $selectie["selectGeboortedatum"] = false;
          }
          if(!empty($_GET['selectEmail'])){
            $selectie["selectEmail"] = ($_GET['selectEmail'] === 'true');
          }else{
            $selectie["selectEmail"] = false;
          }
          if(!empty($_GET['selectAccountType'])){
            $selectie["selectAccountType"] = ($_GET['selectAccountType'] === 'true');
          }else{
            $selectie["selectAccountType"] = false;
          }
          if(!empty($_GET['selectBeoordeling'])){
            $selectie["selectBeoordeling"] = ($_GET['selectBeoordeling'] === 'true');
          }else{
            $selectie["selectBeoordeling"] = false;
          }
          if(!empty($_GET['selectStatus'])){
            $selectie["selectStatus"] = ($_GET['selectStatus'] === 'true');
          }else{
            $selectie["selectStatus"] = false;
          }
      }
    }

    echo http_build_query($selectie) . "\n";
    
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
        <?php if($selectie["selectVoornaam"]){ echo 'checked';} ?> >
        Voornaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAchternaam" value="true" class="form-check-input"
        <?php if($selectie["selectAchternaam"]){ echo 'checked';} ?> >
        Achternaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectGebruikersnaam" value="true" class="form-check-input"
        <?php if($selectie["selectGebruikersnaam"]){ echo 'checked';} ?> >
        Gebruikersnaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectEmail" value="true" class="form-check-input" 
        <?php if($selectie["selectEmail"]){ echo 'checked';} ?> >
        Email
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectGeboortedatum" value="true" class="form-check-input"
        <?php if($selectie["selectGeboortedatum"]){ echo 'checked';} ?> >
        Geboortedatum
      </label>
      <!--<label class="form-check-label selectie">
        <input type="checkbox" name="selectBeoordeling" value="true" class="form-check-input" 
        <?php// if($selectie["selectBeoordeling"]){ echo 'checked';} ?> >
        Beoordeling
      </label>-->
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectStatus" value="true" class="form-check-input" 
        <?php if($selectie["selectStatus"]){ echo 'checked';} ?> >
        Status
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectPostcode" value="true" class="form-check-input" 
        <?php if($selectie["selectPostcode"]){ echo 'checked';} ?> >
        Postcode
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAdresregel1" value="true" class="form-check-input" 
        <?php if($selectie["selectAdresregel1"]){ echo 'checked';} ?> >
        Adres regel 1
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectAdresregel2" value="true" class="form-check-input" 
        <?php if($selectie["selectAdresregel2"]){ echo 'checked';} ?> >
        Adres regel 2
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectPlaatsnaam" value="true" class="form-check-input" 
        <?php if($selectie["selectPlaatsnaam"]){ echo 'checked';} ?> >
        Plaatsnaam
      </label>
      <label class="form-check-label selectie">
        <input type="checkbox" name="selectLand" value="true" class="form-check-input" 
        <?php if($selectie["selectLand"]){ echo 'checked';} ?> >
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
                    if($selectie["selectVoornaam"]){ echo '<th>Voornaam</th>';}
                    if($selectie["selectAchternaam"]){ echo '<th>Achternaam</th>';}
                    if($selectie["selectGebruikersnaam"]){ echo '<th>Gebruikersnaam</th>';}
                    if($selectie["selectEmail"]){ echo '<th>Email</th>';}
                    if($selectie["selectGeboortedatum"]){ echo '<th>Geboorte datum</th>';}
                    if($selectie["selectAccountType"]){ echo '<th>Account type</th>';}
                    //if($selectBeoordeling){ echo '<th>Beoordeling</th>';}
                    if($selectie["selectStatus"]){ echo '<th>Status</th>';}
                    if($selectie["selectLand"]){ echo '<th>Land</th>';}
                    if($selectie["selectPlaatsnaam"]){ echo '<th>Plaatsnaam</th>';}
                    if($selectie["selectPostcode"]){ echo '<th>Postcode</th>';}
                    if($selectie["selectAdresregel1"]){ echo '<th>Adres regel 1</th>';}
                    if($selectie["selectAdresregel2"]){ echo '<th>Adres regel 2</th>';}
                    ?>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $data = $db->prepare("SELECT voornaam, achternaam, gebruikersnaam, emailadres, geboortedatum, typegebruiker, /* beoordeling, Not yet implented*/Accountstatussen.omschrijving AS status, land, plaatsnaam, postcode, adresregel1, adresregel2 
                                                        FROM Gebruikers
                                                        INNER JOIN Accountstatussen 
                                                          ON Gebruikers.statusID=Accountstatussen.ID
                                                        ");
                        $data->execute(array($selectie["sorteerOp"]));  
                        $result=$data->fetchAll();

                        $count=count($result);
                        echo $count;    

                       foreach($result as $row){
                          $gebruikersnaam ="$row[gebruikersnaam]";
                          echo "<tr onclick=\"document.location='koper.php?gebruikersnaam=".$gebruikersnaam."' \" >";
                          if($selectie["selectVoornaam"]){ echo "<td>$row[voornaam]</te>";}
                          if($selectie["selectAchternaam"]){ echo "<td>$row[achternaam]</td>";}
                          if($selectie["selectGebruikersnaam"]){ echo "<td>".$gebruikersnaam."</td>";}
                          if($selectie["selectEmail"]){ echo "<td>$row[emailadres]</td>";}
                          if($selectie["selectGeboortedatum"]){ echo "<td>$row[geboortedatum]</td>";}
                          if($selectie["selectAccountType"]){ echo "<td>$row[typegebruiker]</td>";}
                          //if($selectBeoordeling){ echo '<td>Beoordeling</td>';}
                          if($selectie["selectStatus"]){ echo "<td>$row[status]</td>";}
                          if($selectie["selectLand"]){ echo "<td>$row[land]</td>";}
                          if($selectie["selectPlaatsnaam"]){ echo "<td>$row[plaatsnaam]</td>";}
                          if($selectie["selectPostcode"]){ echo "<td>$row[postcode]</td>";}
                          if($selectie["selectAdresregel1"]){ echo "<td>$row[adresregel1]</td>";}
                          if($selectie["selectAdresregel2"]){ echo "<td>$row[adresregel2]</td>";}
                          echo "</tr>";
                        }
                      ?>
                    </tbody>
                </table>
            </article>
        </section>
    </main>
  </body>
</html>
