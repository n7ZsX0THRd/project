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

            .rubriek_column{
              position:relative;
            }
            .rubriek_char{
              position:absolute;
              left:-30px;
              border-right:2px solid orange;
              padding-top:5px;
              height: 47px;
              width: 35px;
              -webkit-box-shadow: 3px 0 7px -2px #888;
              box-shadow: 3px 0 7px -2px #888;
              -webkit-border-top-right-radius: 50%;
              -webkit-border-bottom-right-radius: 50%;
              -moz-border-radius-topright: 50%;
              -moz-border-radius-bottomright: 50%;
              border-top-right-radius: 50%;
              border-bottom-right-radius: 50%;
            }
            .anchor {
                display: block;
                position: relative;
                top: -120px;
                visibility: hidden;
            }
            .pagination{
              background-color: white !important;
              z-index:1000 !important;
            }
            .rubriek_column:first-of-type{
              margin-top:50px;
            }
            @media(max-width:768px){
              .page-nolink{
                display:none;
              }
              .rubriek_char{
                display:none;
              }
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
                    <ul id="sticky" class="pagination">
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
                                echo '<section  class="row rubriek_column">
                                  <a class="anchor" id="'.$char.'"></a>';
                                echo '<h1 class="rubriek_char">'.$char.'</h1>';
                            }
                            echo '<article class="col-md-4">';
                            echo'<h2>'.$rubriek[0].'</h2>';
                            echo '<ul>';
                            foreach($rubriek as $key => $subRubriek){
                                if (!$key==0){
                                    echo '<li><a href="rubriek.php?rubriek='.$key.'">'.$subRubriek.'</a></li>';
                                }
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
        </main>


    <?php include 'php/includes/footer.php' ?>

    
    <script src="js/jquery.drilldown.min.js"></script>
    <script>
      $('.drilldown').drilldown();
    </script>
    <script src="js/jquery.sticky.js"></script>
    <script>
    $(document).ready(function(){
      $("#sticky").sticky({topSpacing:70});
    });
    </script>
  </body>
</html>
