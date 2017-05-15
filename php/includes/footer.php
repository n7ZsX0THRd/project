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
            <?php if (!isUserLoggedIn($db)){
              ?>
              <li><a href="registreer.php">Registreren</a></li>
              <li><a href="login.php">Inloggen</a></li>
              <?php
            } else{ ?>
              <li><a href="profiel.php">Profiel</a></li>
              <?php
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
