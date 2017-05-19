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

        <title>Veilingsite - Eenmaal Andermaal</title>
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
       </style>
  </head>

  <body>

    <?php include 'php/includes/header.php' ?>
        <main class="container">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                <?php
                    foreach(range('A','Z') as $i) {
                        echo '<li class="page-item"><a class="page-link" href="#'.$i.'">'.$i.'</a></li>';
                    }

                ?>
                </ul>
            </nav>
            <?php 
                foreach(range('A','Z') as $i) {
                    echo '<section id="'.$i.'" class="row">';
                    echo '<h1>'.$i.'</h1>';
                    for ($i=0; $i<3; $i++){
                        echo '<article class="col-md-4">';
                        echo '<h2>Rubriek</h2>';
                        $nSubRubrieken = rand ( 1 , 10 );
                        for ($j=0; $j<$nSubRubrieken; $j++){
                            echo '<p>Sub-rubriek</p>';
                        }
                        echo '</article>'; 
                    }
                    echo'<div class="col-md-12">';
                    echo '<hr>';
                    echo '</div>';
                    echo '</section>';
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
  </body>
</html>
