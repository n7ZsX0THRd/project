<h3></h3>
<ul class="menubar">
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
    <li>
      <a href="">Gebruiker</a>
    </li>
  </ul>
  <?php } ?>
</ul>
