<ul class="nav navbar-nav header-navbar">
  <!--<li class="active"><a href="#">Home</a></li>
  <li><a href="#about">About</a></li>
  <li><a href="login.php">Inloggen</a></li>-->
  <?php
  $files = glob('php/pages/menu/*.{html,php}', GLOB_BRACE);
  foreach($files as $file) {
    if($file !== 'index.php')
      echo '<li><a href="index.php?page='.pathinfo($file)['filename'].'">'.ucfirst(pathinfo($file)['filename']).'</a></li>';
  }
  ?>
</ul>
