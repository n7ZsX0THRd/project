<?PHP
session_start();
include ('php/database.php');
pdo_connect();

if (!(isset($_SESSION['email']) != '')) {
  session_destroy();
  header ("Location: login.php");
}

$email = $_SESSION['email'];
$query="SELECT TOP(1) * FROM Gebruikers WHERE emailadres = '$email'";
$result = $db->query($query)->fetchall()[0];


if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(update_user($_POST,$db))
  {
  $_SESSION['email'] = $_POST['r_email'];
  header('location: index.php');
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Profiel - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
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
              <div class="form-group">
                <label for="exampleInputEmail1">Email</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email" value="<?php echo $result['emailadres']?>">
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-5">
                    <label for="exampleInputEmail1">Voornaam</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Voornaam" value="<?php echo $result['voornaam']?>">
                  </div>
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Achternaam</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Achternaam" value="<?php echo $result['achternaam']?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Dag</label>
                    <select class="form-control">
                      <option selected disabled>Dag</option>
                      <?php for ($i = 1; $i <= 31; $i++) { ?>
                          <option><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Maand</label>
                    <select class="form-control">
                      <option selected disabled>Maand</option>
                      <?php
                      $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
                      foreach ($months as &$value)
                      {
                        ?>
                          <option><?php echo $value; ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Jaar</label>
                    <select class="form-control">
                      <option selected disabled>Jaar</option>
                      <?php for ($i = date("Y"); $i >= 1900; $i--) { ?>
                          <option><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Adres</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Adres" value="<?php echo $result['adresregel1']?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Telefoonnummer</label>
                <input type="" class="form-control" id="exampleInputEmail1" placeholder="Telefoonnummer." >
              </div>

          </div>
          <div class="col-lg-6">

              <div class="form-group">
                <div class="row">
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Adres</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Adres">
                  </div>
                  <div class="col-lg-1">
                  </div>
                  <div class="col-lg-4">
                    <div class="square-image-fix" data-toggle="modal" data-target="#myModal">
                      <div class="edit-user-icon"><span class="glyphicon glyphicon-edit"></span></div>
                      <img src="images/users/johndoe.jpg" id="showImageModal" class="img-responsive img-circle">
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
                      <textarea class="form-control" rows="3" style="max-width:100%;" placeholder="Biografie"></textarea>
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
              <label for="exampleInputPassword1">Bevestig je wachtwoord</label>
              <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Wachtwoord">
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-orange">Wijzigingen opslaan</button>
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
</body>
</html>
