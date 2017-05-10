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
    //include 'php/includes/header.php';            
    require_once('php/database.php'); 
    pdo_connect();

  
    $selectie = array( // default values when there's no get request'
      "sorteerOp" => "achternaam",
      "pagina" => 0,
      "selectVoornaam" => true,
      "selectAchternaam" => true,
      "selectGebruikersnaam" => false,
      "selectGeboortedatum" => false,
      "selectBeoordeling" => false,
      "selectStatus" => true,
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
            $selectie["sorteerOp"] = 'achternaam';
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

    //echo http_build_query($selectie) . "\n";
    
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
  
    </aside>
            <article class="col-md-8">
                <h2> Gebruikers </h2>
                <table class="table table-hover">
                    <thead>
                    <tr>
                    <?php 
                    if($selectie["selectVoornaam"]){ 
                      ?> 
                      <th>
                        <label class="header">
                          <input type="radio" name="sorteerOp" id="voornaam" value="voornaam" 
                          <?php if($selectie["sorteerOp"]=="voornaam"){ echo 'checked';} ?> >
                          <span>Voornaam</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectAchternaam"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="achternaam" 
                        <?php if($selectie["sorteerOp"]=="achternaam"){ echo 'checked';} ?> >
                        <span>Achternaam</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectGebruikersnaam"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="gebruikersnaam" 
                        <?php if($selectie["sorteerOp"]=="gebruikersnaam"){ echo 'checked';} ?> >
                        <span>Gebruiker</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectEmail"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="emailadres" 
                        <?php if($selectie["sorteerOp"]=="emailadres"){ echo 'checked';} ?> >
                        <span>Email</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectGeboortedatum"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="geboortedatum"
                        <?php if($selectie["sorteerOp"]=="geboortedatum"){ echo 'checked';} ?> >
                        <span>Geboortedatum</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectAccountType"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="accountType"
                        <?php if($selectie["sorteerOp"]=="accountType"){ echo 'checked';} ?> >
                        <span>Type account</span>
                        </label>
                      </th>
                      <?php
                    }
                    /*if($selectie["selectBeoordeling"]){ 
                     
                    }*/
                    if($selectie["selectStatus"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="status" 
                        <?php if($selectie["sorteerOp"]=="status"){ echo 'checked';} ?> >
                        <span>Status</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectLand"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="land" 
                        <?php if($selectie["sorteerOp"]=="land"){ echo 'checked';} ?> >
                        <span>Land</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectPlaatsnaam"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="plaatsnaam" 
                        <?php if($selectie["sorteerOp"]=="plaatsnaam"){ echo 'checked';} ?> >
                        <span>Plaats</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectPostcode"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="postcode" 
                        <?php if($selectie["sorteerOp"]=="postcode"){ echo 'checked';} ?> >
                        <span>Postcode</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectAdresregel1"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="adresregel1" 
                        <?php if($selectie["sorteerOp"]=="adresregel1"){ echo 'checked';} ?> >
                        <span>Adres regel 1</span>
                        </label>
                      </th>
                      <?php
                    }
                    if($selectie["selectAdresregel2"]){ 
                      ?> 
                      <th>
                        <label class="header">
                        <input type="radio" name="sorteerOp" value="adresregel2" 
                        <?php if($selectie["sorteerOp"]=="adresregel2"){ echo 'checked';} ?> >
                        <span>Adres regel 2</span>
                        </label>
                      </th>
                      <?php
                    }
                    ?>
                    </tr>
                    </thead>
                    </form>
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
                        $sorteerOp=$selectie["sorteerOp"];
                        global $sorteerOp;
                        $sorteerOp=$selectie["sorteerOp"];
                        echo $count;
                        
                          sort($result);
                          function cmp($a, $b)
                          {
                            global $sorteerOp;
                              return strcmp($a[$sorteerOp], $b[$sorteerOp]);
                          }

                          usort($result, "cmp");
                          
                          //array_multisort($sort['event_type'], SORT_DESC, $sort['title'], SORT_ASC,$mylist);

                       foreach($result as $row){
                          $gebruikersnaam ="$row[gebruikersnaam]";
                          echo "<tr onclick=\"document.location='gebruiker.php?gebruikersnaam=".$gebruikersnaam."' \" >";
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
    </script>
    <script type='text/javascript'>

 $(document).ready(function() { 
   $('input[name=sorteerOp]').change(function(){
        $('form').submit();
   });
  });

</script>

  </body>
</html>


