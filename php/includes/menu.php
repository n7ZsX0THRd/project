<?php
/*
  iProject Groep 2
  30-05-2017

  file: menu.php
  purpose:
    Main Menu
*/
?>
<ul class="nav navbar-nav header-navbar">
  <!--<li class="active"><a href="#">Home</a></li>
  <li><a href="#about">About</a></li>
  <li><a href="login.php">Inloggen</a></li>-->
  <li><a href="index.php">Home</a></li>
  <li><a href="rubriek.php">Zoeken</a></li>

  <?php if (isUserLoggedIn($db)){ ?>
    <li><a href="account.php"><?php echo getLoggedInUser($db)['gebruikersnaam']; ?></a></li>
    <li><a href="php/logout.php">Uitloggen</a></li>
  <?php }else{?>
    <li><a href="login.php">Inloggen</a></li>
    <li><a href="registreer.php">Registreren</a></li>
  <?php }?>
</ul>
