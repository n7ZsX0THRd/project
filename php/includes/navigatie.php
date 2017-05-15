<!-- When you include this you need to have the $page file in main -->

<div class="col-md-3 col-lg-2 col-sm-3 sidebar"> 
    <ul class="menubar">
    <?
    switch($page){
        case 'index':
        { ?>
            <h3>Categorieën</h3>
            <li>
                <a href="">Audio</a>
            </li>
            <li class="toggle-sub">
                <a href="">Auto's</a>
            </li>
            <ul class="sub">
                <li>
                <a href="">BMW</a>
                </li>
                <li>
                <a href="">Mercedes-Benz</a>
                </li>
            </ul>
            <li class="toggle-sub">
                <a href="">Computers</a>
            </li>
            <ul class="sub">
                <li>
                <a href="">Dell</a>
                </li>
                <li>
                <a href="">HP</a>
                </li>
            </ul>
            <li>
                <a href="">Diversen</a>
            </li>
        <?php }
        break;

        case 'profiel':
        { ?>
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
                <?php if($result['typegebruiker'] ==3){?>
                <li class="toggle-sub">
                <a href="gebruikers.php">Beheerpanel</a>
                </li>
                <?php } ?>
            </ul>
        <?php }
        break;

        default:
        { ?>
            <h3>Navigatie 404</h3>
            <li>
                <a href="">404</a>
            </li>
            <li class="toggle-sub">
                <a href="">404</a>
            </li>
            <ul class="sub">
                <li>
                <a href="">404</a>
                </li>
                <li>
                <a href="">404</a>
                </li>
            </ul>
        <?php }
    }
    ?>
    </ul>
</div>
