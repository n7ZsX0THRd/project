<?php
/*
  iProject Groep 2
  30-05-2017

  file: footer.php
  purpose:
  Page footer
*/
?>
<a id="to-top" href="#" class="btn btn-dark btn-lg"><i class="fa fa-chevron-up fa-fw fa-1x"></i></a>
<div class="footer">
  <div class="container">

      <div class="row footer_content">
        <div class="col-lg-3 col-xs-6 col-md-3 col-sm-3">
          <a href="index.php"><img src="images/hamerkleur.png"></a>
        </div>
        <div class="col-lg-3 col-xs-6 col-md-3 col-sm-3">
          <h1>Start hier</h1>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="rubrieken_overzicht.php">Alle rubrieken</a></li>
            <?php if (!isUserLoggedIn($db)){
              ?>
              <li><a href="registreer.php">Registreren</a></li>
              <li><a href="login.php">Inloggen</a></li>
              <?php
            } else{ ?>
              <li><a href="profiel.php">Profiel</a></li>
              <?php
              if(isUserBeheerder($db)){
                ?>
                  <li><a href="gebruikers.php">Gebruikers</a></li>
                <?php
              }
            }
            ?>
          </ul>
        </div>
        <div class="col-lg-3 col-xs-6 col-md-3 col-sm-3">
          <h1>Over ons</h1>
          <ul>
            <li><a href="">Bedrijfsinformatie</a></li>
            <li><a href="pdf/voorwaarden.pdf">Algemene voorwaarden</a></li>
          </ul>
        </div>
        <div class="col-lg-3 col-xs-6 col-md-3 col-sm-3">
          <h1>Support</h1>
          <ul>
            <li><a href="">Veel gestelde vragen</a></li>
            <li><a href="">Contact</a></li>
          </ul>
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
//#to-top button appears after scrolling
var fixed = false;
$(document).scroll(function() {
    if ($(this).scrollTop() > 250) {
        if (!fixed) {
            fixed = true;
            // $('#to-top').css({position:'fixed', display:'block'});
            $('#to-top').show("slow", function() {
                $('#to-top').css({
                    position: 'fixed',
                    display: 'block'
                });
            });
        }
    } else {
        if (fixed) {
            fixed = false;
            $('#to-top').hide("slow", function() {
                $('#to-top').css({
                    display: 'none'
                });
            });
        }
    }
});
</script>
