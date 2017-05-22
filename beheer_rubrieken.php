<?php
    session_start();

    include ('php/database.php');
    include ('php/user.php');
    pdo_connect();


    if (!empty($_POST)){
        if(!empty($_POST['rubriek'])){
            $selectedRubriek = $_POST['rubriek'];

            $data = $db->prepare("  SELECT rubrieknaam, rubrieknummer
                                    FROM Rubriek
                                    WHERE parentRubriek = ?
                                    ORDER BY parentRubriek ASC,rubrieknaam ASC
                                                                ");
            $data->execute(array($selectedRubriek));
            $subRubrieken=$data->fetchAll(); 

            if(!empty($_POST['sub-rubriek'])){
                $selectedSubRubriek = $_POST['sub-rubriek'];

                $data = $db->prepare("  SELECT *
                                    FROM Rubriek
                                    WHERE parentRubriek = ?
                                    ORDER BY parentRubriek ASC,rubrieknaam ASC
                                                                ");
                $data->execute(array($selectedSubRubriek));
                $subSubRubrieken=$data->fetchAll(); 
                if(!empty($_POST['sub-sub-rubriek'])){
                    $selectedSubSubRubriek = $_POST['sub-sub-rubriek'];
                    
                    $data = $db->prepare("  SELECT *
                                        FROM Rubriek
                                        WHERE parentRubriek = ?
                                        ORDER BY parentRubriek ASC,rubrieknaam ASC
                                                                    ");
                    $data->execute(array($selectedSubSubRubriek));
                    $subSubSubRubrieken=$data->fetchAll();
                    if(!empty($_POST['sub-sub-sub-rubriek'])){
                         $selectedSubSubSubRubriek = $_POST['sub-sub-sub-rubriek'];
                    }else{
                        if (!empty($subSubSubRubrieken[0]["rubrieknummer"])){
                            $selectedSubSubSubRubriek =  $subSubSubRubrieken[0]["rubrieknummer"];
                        }
                    }
                }else{
                    if (!empty($subSubRubrieken[0]["rubrieknummer"])){
                        $selectedSubSubRubriek =  $subSubRubrieken[0]["rubrieknummer"];
                    }
                }
            }else{
                if (!empty($subRubrieken[0]["rubrieknummer"])){
                    $selectedSubRubriek =  $subRubrieken[0]["rubrieknummer"];
                }
            }

        } else {
            $selectedRubriek = -1;
        }
    }else {
        $selectedRubriek = -1;    
    }
    global $db;
    $data = $db->query("SELECT rubrieknaam, rubrieknummer
                        FROM Rubriek
                        WHERE parentRubriek = -1 OR parentRubriek is NULL
                        ORDER BY parentRubriek ASC, rubrieknaam ASC");
                        /*
        while ($row = $data->fetch()){
            echo "$row[rubrieknaam]</br>";
        }*/

    $hoofdRubrieken = $data->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>
        <title>Beheer rubrieken - Eenmaal Andermaal</title>

                <script>
                function change(){
                    document.getElementById("rubriekenForm").submit();
                }
                </script>

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
        <section class="row"> 
            <h1> Beheer </h1>
            
        </section>
        <section class="row">
            <form id="rubriekenForm" method="POST">
            <article class="col-md-3">
                    <div class="form-group">
                        <label for="sel1">Selecteer rubriek:</label>
                        <select class="form-control" id="rubriek" name="rubriek" onchange="change()">
                                <?php
                                //$selectedRubriek = $_POST['rubriek'];
                                foreach($hoofdRubrieken as $rubriek){
                                    echo '<option name="rubriek" value="'.$rubriek["rubrieknummer"].'"'
                                    . ($selectedRubriek == $rubriek["rubrieknummer"] ? 'selected="selected"' : '' ) .'
                                    >'.$rubriek["rubrieknaam"].'</option>';
                                }
                                ?>
                        </select>
                    </div>
                    <hr>
                </article>
                <article class="col-md-3">
                    <div class="form-group">
                        <label for="sel1">Selecteer sub-rubriek:</label>
                        <select class="form-control" id="sub-rubriek" name="sub-rubriek" onchange="change()">
                                <?php
                                //$selectedRubriek = $_POST['sub-rubriek'];
                                foreach($subRubrieken as $rubriek){
                                    echo '<option name="sub-rubriek" value="'.$rubriek["rubrieknummer"].'"'
                                    . ($selectedSubRubriek == $rubriek["rubrieknummer"] ? 'selected="selected"' : '' ) .'
                                    >'.$rubriek["rubrieknaam"].'</option>';
                                }
                                ?>
                        </select>
                    </div>
                    <hr>
                </article>
                <article class="col-md-3">
                    <div class="form-group">
                        <label for="sel1">Selecteer sub-sub-rubriek:</label>
                        <select class="form-control" id="sub-sub-rubriek" name="sub-sub-rubriek" onchange="change()">
                                <?php
                               // $selectedRubriek = $_POST['sub-sub-rubriek'];
                                foreach($subSubRubrieken as $rubriek){
                                    echo '<option name="sub-sub-rubriek" value="'.$rubriek["rubrieknummer"].'"'
                                    . ($selectedSubSubRubriek == $rubriek["rubrieknummer"] ? 'selected="selected"' : '' ) .'
                                    >'.$rubriek["rubrieknaam"].'</option>';
                                }
                                ?>
                        </select>
                    </div>
                    <hr>
                </article>
                <article class="col-md-3">
                    <div class="form-group">
                        <label for="sel1">Selecteer sub-sub-sub-rubriek:</label>
                        <select class="form-control" id="sub-sub-sub-rubriek" name="sub-sub-sub-rubriek" onchange="change()" 
                                <?php
                                /*if (is_null($selectedSubSubRubriek)){
                                    echo 'disabled';
                                }*/
                                
                                ?>
                                >
                                <?php
                               // $selectedRubriek = $_POST['sub-sub-rubriek'];
                                foreach($subSubSubRubrieken as $rubriek){
                                    echo '<option name="sub-sub-sub-rubriek" value="'.$rubriek["rubrieknummer"].'"'
                                    . ($selectedSubSubSubRubriek == $rubriek["rubrieknummer"] ? 'selected="selected"' : '' ) .'
                                    >'.$rubriek["rubrieknaam"].'</option>';
                                }
                                ?>
                        </select>
                    </div>
                    <hr>
                </article>
                </form>
            </section>

                    <section class="row">
            <article class="col-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="nieuwe-rubriek">
                    <label for="selectie">
                        <div class="radio">
                            <label><input type="radio" name="optradio">Voeg toe</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Wijzig</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Verwijder</label>
                        </div>
                    </label>
                </div>
            </article>
            <article class="col-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="nieuwe-rubriek">
                    <label for="selectie">
                        <div class="radio">
                            <label><input type="radio" name="optradio">Voeg toe</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Wijzig</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Verwijder</label>
                        </div>
                    </label>
                </div>
            </article>
            <article class="col-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="nieuwe-rubriek">
                    <label for="selectie">
                        <div class="radio">
                            <label><input type="radio" name="optradio">Voeg toe</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Wijzig</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Verwijder</label>
                        </div>
                    </label>
                </div>
            </article>
            <article class="col-md-3">
                <div class="form-group">
                    <input type="text" class="form-control" id="nieuwe-rubriek">
                    <label for="selectie">
                        <div class="radio">
                            <label><input type="radio" name="optradio">Voeg toe</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Wijzig</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" name="optradio">Verwijder</label>
                        </div>
                    </label>
                </div>
            </article>
        </section>
        <section class="row"> 
            <button class="btn btn-orange widebutton menubutton"  type="submit"  name="Accept" >
                <i class="glyphicon glyphicon-ok"></i>
                Pas toe
            </button>
        </section>
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
  </body>
</html>
