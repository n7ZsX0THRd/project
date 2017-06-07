<?php
/*
  iProject Groep 2
  30-05-2017

  file: sidebar.php
  purpose:
  Left sidebar for loggedIn User
*/
  $result = getLoggedInUser($db);
?>

<h3></h3>
<ul class="menubar">
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'ru'){ echo 'active'; }?>">
    <a href="rubriek.php">Rubrieken</a>
  </li>
  <ul class="sub">
    <li>
      <a href="rubrieken_overzicht.php">Bekijk alles</a>
    </li>
    <li>
      <a href="rubriek.php">Zoeken</a>
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
      <a href="veilingtoevoegen.php">Verkopen</a>
    </li>
  </ul>
  <li class="toggle-sub <?php if($_SESSION['menu']['sub'] === 'ma'){ echo 'active'; }?>">
    <a href="account.php">Mijn Account</a>
  </li>
  <ul class="sub">
    <li>
      <a href="biedingen.php">Biedingen</a>
    </li>
    <li>
      <a href="veilingen.php">Veilingen</a>
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
