<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

/*
if (!(isset($_SESSION['email']) != '')) {
  session_destroy();
  header ("Location: login.php");
}
*/

$landcodes = $db->query("SELECT lnd_code FROM Landen ORDER BY lnd_code ASC");
$email = $_SESSION['email'];
$query="SELECT TOP(1)
       [gebruikersnaam]
      ,[voornaam]
      ,[achternaam]
      ,[adresregel1]
      ,[adresregel2]
      ,[postcode]
      ,[plaatsnaam]
      ,[land]
      ,datepart(month,[geboortedatum]) AS geboortedag
	    ,datepart(day,[geboortedatum]) AS geboortemaand
	    ,datepart(year,[geboortedatum]) AS geboortejaar
      ,[emailadres]
      ,[wachtwoord]
      ,[vraag]
      ,[antwoordtekst]
      ,[typegebruiker]
      ,[statusID]
      ,[bestandsnaam]FROM Gebruikers WHERE emailadres = '$email'";
$result = $db->query($query)->fetchall()[0];

$gebruiker = $result['gebruikersnaam'];
$query2 ="SELECT telefoonnummer FROM Gebruikerstelefoon WHERE gebruikersnaam = '$gebruiker'";
$result2 = $db->query($query2)->fetchall()[0];


if(!empty($result['bestandsnaam'])) {
  $image = $result['bestandsnaam'];
}
else {
  $image = "geenfoto/geenfoto.png";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
$_POST['p_username']=$result['gebruikersnaam'];

if(isset($_POST)){
  var_dump($_POST);
  if(update_user($_POST,$db))
    {
    //header('location: profiel.php');
    }

}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Profiel - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
  </head>
  <body>

    <?php include 'php/includes/header.php' ?>

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
        <?php if($result['typegebruiker'] ==3){?>
        <li class="toggle-sub">
          <a href="gebruikers.php">Beheerpanel</a>
        </li>
        <?php } ?>
      </ul>
    </div>
    <div class="col-md-8 col-lg-10 col-sm-8">
      <div class="container-fluid  content_col">
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

        <div class="row content_top_offset">
          <div class="col-lg-6" style="border-right:1px solid #e7e7e7;">
            <form method="post" enctype="multipart/form-data" action="">

              <!-- email -->
              <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <?php if (isset($_GET['wijzig'])==true){  ?>
                <input type="email" name="p_email" class="form-control" id="email" placeholder="Email" value="<?php echo $result['emailadres']?>">
                <?php }else{ ?>
                  <div class="pflijn">
                      <?php echo $result['emailadres']?>
                  </div>
                <?php } ?>
              </div>

              <!-- Voornaam en achternaam -->
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Voornaam</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <input type="text" class="form-control" name="p_firstname" id="exampleInputEmail1" placeholder="Voornaam" value="<?php echo $result['voornaam']?>">
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['voornaam']?>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="col-lg-8">
                    <label for="exampleInputEmail1">Achternaam</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <input type="text" name="p_lastname" class="form-control" id="exampleInputEmail1" placeholder="Achternaam" value="<?php echo $result['achternaam']?>">
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['achternaam']?>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <!-- geboortedatum -->
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Dag</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <select name="p_birthday" class="form-control">
                      <option selected disabled>Dag</option>
                      <?php for ($i = 1; $i <= 31; $i++) { ?>
                          <option value="<?php echo $i; ?>" <?php if($result['geboortedag'] == $i){  echo 'selected'; } ?>><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['geboortedag']?>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Maand</label>

                      <?php if (isset($_GET['wijzig'])==true){  ?>
                      <select name="p_birthmonth" class="form-control">
                        <option selected disabled>Maand</option>
                        <?php

                        $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
                        if (isset($_GET['wijzig'])==true){  ?>
                        <select name="p_birthmonth" class="form-control">
                          <option selected disabled>Maand</option>
                          <?php
                          $index = 1;
                          foreach ($months as $value)
                          {
                          ?>
                          <option value="<?php echo $index; ?>" <?php if($result['geboortemaand'] == $index){  echo 'selected'; } ?>><?php echo $value; ?></option>

                          <?php
                          $index++;
                          }
                          ?>
                        </select>
                        <?php }else{ ?>
                          <div class="pflijn">
                              <?php echo $months[$result['geboortemaand']-1]; ?>
                          </div>
                        <?php } ?>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Jaar</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <select name="p_birthyear" class="form-control">
                      <option selected disabled>Jaar</option>
                      <?php for ($i = date("Y"); $i >= 1900; $i--) { ?>
                          <option value="<?php echo $i; ?>" <?php if($result['geboortejaar'] == $i){  echo 'selected'; } ?>><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['geboortejaar']?>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <!-- adresgegevens -->
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-8">
                    <label for="exampleInputEmail1">Adres + Huisnr.</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <input name="p_adres" type="text" class="form-control" id="exampleInputEmail1" placeholder="Adres" value="<?php echo $result['adresregel1']?>">
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['adresregel1']?>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Postcode</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <input name="p_zipcode" type="text" class="form-control" id="exampleInputEmail1" placeholder="Postcode" value="<?php echo $result['postcode']?>">
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['postcode']?>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-8">
                    <label for="exampleInputEmail1">Woonplaats</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <input name="p_city" type="text" class="form-control" id="exampleInputEmail1" placeholder="Adres" value="<?php echo $result['plaatsnaam']?>">
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['plaatsnaam']?>
                      </div>
                    <?php } ?>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Landcode</label>
                    <?php if (isset($_GET['wijzig'])==true){  ?>
                    <select class="form-control" name="p_land">
                      <option disabled>Land</option>
                      <?php
                        while ($row = $landcodes->fetch()){
                      ?>
                        <option value="<?php echo $row['lnd_code'];?>" <?php if(($result['land']) == $row['lnd_code']){  echo 'selected'; } ?>><?php echo $row['lnd_code'];?></option>
                      <?php
                        }
                      ?>
                    </select>
                    <?php }else{ ?>
                      <div class="pflijn">
                          <?php echo $result['land']?>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>

              <!-- Telefoonnummer -->
              <div class="form-group">
                <label for="exampleInputEmail1">Telefoonnummer</label>
                <?php if (isset($_GET['wijzig'])==true){  ?>
                <input name="p_tel" class="form-control" id="exampleInputEmail1" placeholder="Telefoonnummer." >
                <?php }else{ ?>
                <div class="pflijn">
                    <?php
                      echo $result2['telefoonnummer'];
                    ?>
                </div>
                <?php } ?>
              </div>

          </div>
          <div class="col-lg-6">

              <div class="form-group">
                <div class="row">
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Gebruikersnaam</label>
                    <div class="pflijn">
                        <?php echo $result['gebruikersnaam']?>
                    </div>
                  </div>
                  <div class="col-lg-1">
                  </div>
                  <div class="col-lg-4">
                    <div class="square-image-fix" data-toggle="modal" data-target="#myModal">
                      <div class="edit-user-icon"><span class="glyphicon glyphicon-edit"></span></div>
                      <img src="images/users/<?php echo $image ?>" id="showImageModal" class="img-responsive img-circle">
                    </div>
                  </div>
                  <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
                    <div class="modal-dialog" role="document">
                       <div class="modal-content">
                         <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <h4 class="modal-title" id="myModalLabel">Verander profielfoto</h4>
                         </div>
                         <div class="modal-body">
                           <div class="form-group">
                             <label for="exampleInputFile">Upload een foto</label>
                             <input type="file" id="exampleInputFile">
                             <p class="help-block">Upload alleen bestanden met png of jpg als bestandstype.</p>
                           </div>
                         </div>
                         <div class="modal-footer">
                           <button type="button" class="btn btn-default" data-dismiss="modal">Annuleren</button>
                           <button type="button" class="btn btn-primary">Opslaan</button>
                         </div>
                       </div>
                     </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-12">
                     <div class="form-group">
                      <label for="exampleInputFile">Biografie</label>
                      <textarea class="form-control" rows="10" style="max-width:100%;" name="p_biografie"  placeholder="Biografie"></textarea>
                     </div>
                  </div>
                </div>
              </div>

          </div>

        </div>

        <div class="row">
          <div class="col-lg-12">
            <hr>
          </div>
          <div class="col-lg-6 col-lg-offset-6">

            <div class="form-group">
              <?php if (isset($_GET['wijzig'])==true){  ?>
              <label for="exampleInputPassword1">Bevestig je wachtwoord</label>
              <input name="confirmpass" type="password" class="form-control" id="exampleInputPassword1" placeholder="Wachtwoord">
              <?php } ?>
            </div>
            <div class="text-right">
              <?php if (isset($_GET['wijzig'])==false){  ?>
              <a href="?wijzig" type="submit" class="btn btn-orange">Wijzig gegevens</a>
              <?php }else{ ?>
              <a href="?" type="submit" class="btn btn-orange">Annuleren</a>
              <button type="submit" class="btn btn-orange" >Wijzingen opslaan</button>
              <?php } ?>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      <?php include 'php/includes/footer.php' ?>

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
</script>
<script>function myAjax(actionvar) {
      $.ajax({
           type: "POST",
           url: 'ajax.php',
           data:{action:actionvar},
           success:function(html) {
             window.location.href = "profiel.php";
             alert(html);
           }
      });
 } </script>
</body>
</html>
