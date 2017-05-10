<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

if(isset($_GET['gebruikersnaam']) && !empty($_GET['gebruikersnaam']) && isset($_GET['code']) && !empty($_GET['code'])){
    // Verify data
    $gebruikersnaam = $_GET['gebruikersnaam'];
    $code = $_GET['code'];
}else{
    // Invalid approach
    header ('location: index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
        include ('php/includes/default_header.php');
        ?>
        
        <title>Verifieer - Eenmaal Andermaal</title>
    </head>

    <body>
        <?php 
        include ('php/includes/header.php');
        ?>
        
        <div class="container">
        <?php
            $dbs = $db->prepare("SELECT * FROM Activatiecodes WHERE gebruikernaam=?");
            $dbs->execute($gebruikersnaam);
            $result = $dbs->fetchAll();
            if ($_GET['gebruikersnaam'] == $result['gebruikersnaam'] && $_GET['code'] == $result['activatiecode']) {
                echo 'Je code klopt!';
            }else{
                echo 'Je code klopt niet!';
            }
                
        ?>
        </div>
                
        <?php 
        include ('php/includes/footer.php');
        ?>
        
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
        <script src="bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
        <script>
        /*
        $("li.toggle-sub").click(function(evt) {

        evt.preventDefault();
        $(this).children("span").toggleClass('glyphicon-menu-right');
        $(this).children("span").toggleClass('glyphicon-menu-down');
        $(this).children(".sub").toggle();
        });
        */
        </script>
    </body>
</html>