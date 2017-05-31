<?php
/*
  iProject Groep 2
  30-05-2017

  file: header.php
  purpose:
  Navigation
*/
?>
    <nav class="navbar navbar-inverse navbar-fixed-top header-style">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">
            <div style="float:left;">
              <img src="images/logo.png" width="65">
            </div>
            <div>
              <span>Eenmaal Andermaal</span>
              <p>De beste veilingsite van Nederland</p>
            </div>
          </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <?php include 'menu.php' ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
