<?php
session_start();
$_SESSION['menu']['sub'] = 'bp';

include ('php/database.php');
include ('php/user.php');
pdo_connect();


if(isUserBeheerder($db) == false){
  header("Location: index.php");
}

$result = getLoggedInUser($db);


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
  "selectLand" => false,
  "zoeken" => "",
  "gebruiker-soort" => "alle"
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
      if(!empty($_GET['zoeken'])){
        $selectie["zoeken"] = htmlspecialchars($_GET['zoeken']);
      } else {
        $selectie["zoeken"] = "";
      }
      if(!empty($_GET['gebruiker-soort'])){
        $selectie["gebruiker-soort"] = htmlspecialchars($_GET['gebruiker-soort']);
      } else {
        $selectie["gebruiker-soort"] = "beheerder";
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
        <?php include 'php/includes/default_header.php'; ?>
        <link href="css/dashboard.css" rel="stylesheet">
        <title>Beheerpanel - Eenmaal Andermaal</title>
  </head>

  <body>


    <?php
    include 'php/includes/header.php';
    //echo http_build_query($selectie) . "\n";
    ?>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-lg-2 col-sm-4 sidebar">
          <?php
            include 'php/includes/sidebar.php';
          ?>
          <hr class="menu-hr">
          <form action="gebruikers.php" method="get" class="form-check" >
            <h4>Soort </h4>
            <label><input type="radio" name="gebruiker-soort" value="koper"
            <?php if($selectie["gebruiker-soort"]=="koper"){ echo 'checked';} ?> >
             Kopers</label><br>
            <label><input type="radio" name="gebruiker-soort" value="verkoper"
            <?php if($selectie["gebruiker-soort"]=="verkoper"){ echo 'checked';} ?> >
             Verkopers</label><br>
            <label><input type="radio" name="gebruiker-soort" value="beheerder"
            <?php if($selectie["gebruiker-soort"]=="beheerder"){ echo 'checked';} ?> >
             Beheerders</label><br>
            <label><input type="radio" name="gebruiker-soort" value="alle"
            <?php if($selectie["gebruiker-soort"]=="alle"){ echo 'checked';} ?> >
             Alle</label>

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
              <button class="btn btn-orange widebutton menubutton"  type="submit"  name="Accept" >
                <i class="glyphicon glyphicon-ok"></i>
                Pas toe
              </button>
        </div>
        <div class="col-md-8 col-lg-10 col-sm-8">
          <div class="container-fluid content_col">
            <div class="row navigation-row">
                <p>
                  <a href="index.php">
                    <span class="glyphicon glyphicon-home "></span>
                  </a>
                  <span class="glyphicon glyphicon-menu-right"></span>
                  <a href="">Beheerpanel</a>
                  <span class="glyphicon glyphicon-menu-right"></span>
                  <a href="">Gebruikers</a>
                </p>
            </div>
            <div class="row content_top_offset">
              <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="container-fixed">
                  <div class="row">
                    <div class="inner-addon left-addon">
                        <i class="glyphicon glyphicon-search"></i>
                        <input type="search" id="search" name="zoeken" <?php echo "value='".$selectie["zoeken"]."'"; ?> class="form-control" placeholder="Zoeken..">
                    </div>
                  </div>
                  <div class="row content_top_offset" style="overflow-x:scroll;">
                    <table class="table table-hover" id="table">
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

                            $data = $db->prepare("SELECT voornaam, achternaam, gebruikersnaam, emailadres, geboortedatum,  Accounttype.typegebruiker as soort, /* beoordeling, Not yet implented*/Accountstatussen.omschrijving AS status, land, plaatsnaam, postcode, adresregel1, adresregel2
                                                            FROM Gebruikers
                                                            INNER JOIN Accountstatussen
                                                              ON Gebruikers.statusID=Accountstatussen.ID
                                                            INNER JOIN Accounttype
    	                                                        ON Accounttype.ID=Gebruikers.typegebruiker
                                                            WHERE Accounttype.typegebruiker = ? OR 'alle'= ?
                                                            ");
                            $data->execute(array($selectie["gebruiker-soort"], $selectie["gebruiker-soort"]));
                            $result=$data->fetchAll();

                            $count=count($result);
                            $sorteerOp=$selectie["sorteerOp"];
                            global $sorteerOp;
                            $sorteerOp=$selectie["sorteerOp"];
                            //echo $count;

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
                              if($selectie["selectVoornaam"]){ echo "<td class='col-xs-4'>$row[voornaam]</te>";}
                              if($selectie["selectAchternaam"]){ echo "<td class='col-xs-4'>$row[achternaam]</td>";}
                              if($selectie["selectGebruikersnaam"]){ echo "<td class='col-xs-4'>".$gebruikersnaam."</td>";}
                              if($selectie["selectEmail"]){ echo "<td class='col-xs-4'>$row[emailadres]</td>";}
                              if($selectie["selectGeboortedatum"]){ echo "<td class='col-xs-4'>$row[geboortedatum]</td>";}
                              if($selectie["selectAccountType"]){ echo "<td class='col-xs-4'>$row[typegebruiker]</td>";}
                              //if($selectBeoordeling){ echo '<td>Beoordeling</td>';}
                              if($selectie["selectStatus"]){ echo "<td class='col-xs-4'>$row[status]</td>";}
                              if($selectie["selectLand"]){ echo "<td class='col-xs-4'>$row[land]</td>";}
                              if($selectie["selectPlaatsnaam"]){ echo "<td class='col-xs-4'>$row[plaatsnaam]</td>";}
                              if($selectie["selectPostcode"]){ echo "<td class='col-xs-4'>$row[postcode]</td>";}
                              if($selectie["selectAdresregel1"]){ echo "<td class='col-xs-4'>$row[adresregel1]</td>";}
                              if($selectie["selectAdresregel2"]){ echo "<td class='col-xs-4'>$row[adresregel2]</td>";}
                              echo "</tr>";
                            }
                          ?>
                        </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    <!-- /.container -->
    <?php
      include 'php/includes/footer.php';
    ?>

        <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script src="//rawgithub.com/stidges/jquery-searchable/master/dist/jquery.searchable-1.0.0.min.js"></script>
    <script type='text/javascript'>

   $(document).ready(function() {
     $('input[name=sorteerOp]').change(function(){
          $('form').submit();
     });
  });
  $(function () {
      $( '#table' ).searchable({
          searchType: 'fuzzy'
      });

      $( '#searchable-container' ).searchable({
          searchField: '#container-search',
          selector: '.row',
          childSelector: '.col-xs-4',
          show: function( elem ) {
              elem.slideDown(100);
          },
          hide: function( elem ) {
              elem.slideUp( 100 );
          }
      })
  });
</script>

  </body>
</html>
