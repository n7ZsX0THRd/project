<?php
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>
        <title>Rubrieken overzicht - Eenmaal Andermaal</title>
        <style>
         hr {
                -moz-border-bottom-colors: none;
                -moz-border-image: none;
                -moz-border-left-colors: none;
                -moz-border-right-colors: none;
                -moz-border-top-colors: none;
                border-color: #EEEEEE -moz-use-text-color #FFFFFF;
                border-style: solid none;
                border-width: 1px 0;
                margin: 18px 0;

            }
            .page-nolink{
                background: #efefef!important;
                color: #000 !important;
                cursor:default;
            }


            .page-link:hover{
                background: #5484a4!important;
                color: #FFF !important;
            }
       </style>
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>
        <main class="container">
            <?php
            global $db;
            $data = $db->query("SELECT * 
                                FROM Rubriek 
                                WHERE parentRubriek = -1 or parentRubriek IN (
                                                                SELECT rubrieknummer
                                                                FROM Rubriek
                                                                WHERE parentRubriek = -1 )
                                ORDER BY parentRubriek ASC, volgnr ASC, rubrieknaam ASC");
                                /*
                while ($row = $data->fetch()){
                    echo "$row[rubrieknaam]</br>";
                }*/

                $result = $data->fetchAll();
                $count=count($result);
                //print_r($result);
                $rubrieken = [];
                for ($row = 0; $row < $count; $row++) {
                    //echo "<p><b>Row number $row</b></p>";
                    if ($result[$row]['parentRubriek']==-1){
                        $nummer = $result[$row]['rubrieknummer'];
                        $rubrieken[$nummer][] = $result[$row]['rubrieknaam'];
                        //echo "<li>".$result[$row]['rubrieknaam']."</li>";
                    } else {
                        $parentNummer = $result[$row]['parentRubriek'];
                        $nummer = $result[$row]['rubrieknummer'];
                        $rubrieken[$parentNummer][$nummer] = $result[$row]['rubrieknaam'];
                    }
                }

                ?>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                    <?php
                        foreach(range('A','Z') as $char) {
                            $nRubrieken = 0;
                            foreach($rubrieken as $rubriek){
                                $firstChar =substr ($rubriek[0] , 0 , 1 );
                                if ($char==$firstChar){
                                    $nRubrieken ++;
                                    if($nRubrieken==1){
                                        echo '<li class="page-item"><a class="page-link" href="#'.$char.'">'.$char.'</a></li>';
                                    }
                                }
                            }
                            if($nRubrieken==0){
                                echo '<li class="page-item"><a class="page-nolink">'.$char.'</a></li>';
                            }
                        }

                    ?>
                    </ul>
                </nav>
                <?php
                foreach(range('A','Z') as $char) {
                    $nRubrieken = 0;
                    foreach($rubrieken as $rubriek){
                        $firstChar =substr ($rubriek[0] , 0 , 1 );
                        if ($char==$firstChar){
                            $nRubrieken ++;
                            if($nRubrieken==1){
                                echo '<section id="'.$char.'" class="row">';
                                echo '<h1>'.$char.'</h1>';
                            }
                            echo '<article class="col-md-4">';
                            echo'<h2>'.$rubriek[0].'</h2>';
                            echo '<ul>';
                            foreach($rubriek as $subRubriek){
                                echo '<li>'.$subRubriek.'</li>';
                            }
                            echo '</ul>';
                            echo '</article>'; 
                        }
                    }
                    if ($nRubrieken!=0){
                        echo'<div class="col-md-12">';
                        echo '<hr>';
                        echo '</div>';
                        echo '</section>';
                    }
                }

            ?>

            <nav aria-label="Page navigation example">
                <ul class="pagination">
                <?php
                    foreach(range('A','Z') as $i) {
                        echo '<li class="page-item"><a class="page-link" href="#'.$i.'">'.$i.'</a></li>';
                    }

                ?>
                </ul>
            </nav>
        </main>
        <a id="to-top" href="#" class="btn btn-dark btn-lg"><i class="fa fa-chevron-up fa-fw fa-1x"></i></a>

    <?php include 'php/includes/footer.php' ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
    <script src="js/jquery.drilldown.min.js"></script>
    <script>
      $('.drilldown').drilldown();
    </script>
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
    
    <script>
    //#to-top button appears after scrolling
    var fixed = false;
    $(document).scroll(function() {
        if ($(this).scrollTop() > 250) {
            if (!fixed) {
                fixed = true;
                // $('#to-top').css({position:'fixed', display:'block'});
                $('#to-top').show("slow", function() {
                    $('#to-top').css({
                        position: 'fixed',
                        display: 'block'
                    });
                });
            }
        } else {
            if (fixed) {
                fixed = false;
                $('#to-top').hide("slow", function() {
                    $('#to-top').css({
                        display: 'none'
                    });
                });
            }
        }
    });
    </script>
  </body>
</html>
