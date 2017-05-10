<?php
?>
<ul class="nav navbar-nav header-navbar">
  <!--<li class="active"><a href="#">Home</a></li>
  <li><a href="#about">About</a></li>
  <li><a href="login.php">Inloggen</a></li>-->
  <li><a href="index.php">Home</a></li>

  <?php if (isUserLoggedIn($db)){ ?>
    <li><a href="profiel.php"><?php echo getLoggedInUser($db)['gebruikersnaam']; ?></a></li>
    <li><a href="php/logout.php">Uitloggen</a></li>
  <?php }else{?>
    <li><a href="login.php">Inloggen</a></li>
    <li><a href="registreer.php">Registreren</a></li>
  <?php }?>
</ul>
