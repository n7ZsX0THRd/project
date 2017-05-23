<?php
  $result = getLoggedInUser($db);
?>

<h3></h3>
<ul class="menubar">
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'ru'){ echo 'active'; }?>">
    <a href="rubriek.php">Rubrieken</a>
  </li>
  <ul class="sub">
    <li>
      <a href="">Item 1</a>
    </li>
    <li>
      <a href="">Item 2</a>
    </li>
    <li>
      <a href="">Item 3</a>
    </li>
  </ul>
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'dr'){ echo 'active'; }?>">
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
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'ma'){ echo 'active'; }?>">
    <a href="account.php">Mijn Account</a>
  </li>
  <ul class="sub">
    <li>
      <a href="">Mijn biedingen</a>
    </li>
    <li>
      <a href="">Mijn favorieten</a>
    </li>
    <li>
      <a href="profiel.php">Instellingen</a>
    </li>
    <li>
      <a href="php/logout.php">Uitloggen</a>
    </li>
  </ul>
  <?php if($result['typegebruiker'] ==3){?>
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'bp'){ echo 'active'; }?>">
    <a href="">Beheerpanel</a>
  </li>
  <ul class="sub">
    <li>
      <a href="gebruikers.php">Gebruikers</a>
    </li>
  </ul>
  <?php } ?>
</ul>
