<?PHP
session_start();

include ('php/database.php');
include ('php/user.php');
pdo_connect();

$rootRubriek = -1;
$breadCrumbQuery = $db->prepare("SELECT * FROM dbo.fnRubriekOuders(?) ORDER BY volgorde DESC");
$breadCrumbQuery->execute(array(htmlspecialchars(1)));
$breadCrumb = $breadCrumbQuery->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
  <head>

        <?php include 'php/includes/default_header.php'; ?>

        <title>Profiel - Eenmaal Andermaal</title>

        <link href="css/login.css" rel="stylesheet">
        <link href="css/profilestyle.css" rel="stylesheet">
        <link href="css/veiling.css" rel="stylesheet">

  </head>
  <body>

    <?php include 'php/includes/header.php' ?>

<div class="container">
  <div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12">
      <div class="container-fluid">
        <?php
        if(count($breadCrumb) != 0)
        {
        ?>
          <div class="row navigation-row">
              <p>
                <a class="btn btn-default" href="javascript:history.go(-1)">
                <span class="glyphicon glyphicon-arrow-left"></span>  Overzicht
                </a>
                <a href="index.php" style="padding-left:20px;">
                  <span class="glyphicon glyphicon-home "></span>
                </a>
                <span class="glyphicon glyphicon-menu-right"></span>
                <a href="rubriek.php">Rubrieken</a>
                <?php


                    foreach($breadCrumb as $row)
                    {
                      if($row['rubrieknummer'] != $rootRubriek)
                      {
                      ?>
                        <span class="glyphicon glyphicon-menu-right"></span>
                        <a href="rubriek.php?rubriek=<?php echo $row['rubrieknummer']; ?>"><?php echo $row['rubrieknaam']; ?></a>
                      <?php
                      }
                    }
                ?>
              </p>
          </div>
        <?php
        }
        ?>
        <div class="row content_top_offset">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li role="presentation" class="active"><a href="#veiling" aria-controls="veiling" role="tab" data-toggle="tab">Veiling</a></li>
              <li role="presentation"><a href="#bieden" class="bg-success" aria-controls="bieden" role="tab" data-toggle="tab">Bieden</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="veiling">
                            <div class="col-lg-12 timer_row" >
                              <div class="veiling-countdown">
                                <h1 id="productCountDown">COUNTDOWN</h1>
                              </div>
                              <div class="veiling-titel">
                                <h2>Alle pokemonkaarten van sam voor maar 1 euro. Moet snel weg omdat ik meer skeer voedsel moet kopen!!</h2>
                              </div>
                            </div>
                            <div class="col-lg-4 left_content_row content_top_offset">
                                <div class="row thumb-image">
                                  <div class="carousel slide article-slide" id="article-photo-carousel">

                                      <!-- Wrapper for slides -->
                                      <div class="carousel-inner cont-slider">

                                        <div class="item active">
                                          <img alt="" title="" style="background-image:url(images/vliegtuig.jpg);">
                                        </div>
                                        <div class="item">
                                          <img alt="" title="" style="background-image:url(images/eten.jpg);">
                                        </div>
                                        <div class="item">
                                          <img alt="" title="" style="background-image:url(images/Johny.jpg);">
                                        </div>
                                        <div class="item">
                                          <img alt="" title="" style="background-image:url(images/bmw.jpg);">
                                        </div>
                                      </div>
                                      <!-- Indicators -->
                                      <ol class="carousel-indicators">
                                        <li class="active" data-slide-to="0" data-target="#article-photo-carousel">
                                          <img alt="" style="background-image:url(images/vliegtuig.jpg);">
                                        </li>
                                        <li class="" data-slide-to="1" data-target="#article-photo-carousel">
                                          <img alt="" style="background-image:url(images/eten.jpg);">
                                        </li>
                                        <li class="" data-slide-to="2" data-target="#article-photo-carousel">
                                          <img alt="" style="background-image:url(images/Johny.jpg);">
                                        </li>
                                        <li class="" data-slide-to="3" data-target="#article-photo-carousel">
                                          <img alt="" style="background-image:url(images/bmw.jpg);">
                                        </li>
                                      </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8" style="margin-left:40px; margin-top:20px; width:60%">

                              <p>
                                De SP voelt niets voor het voorstel van D66-leider Alexander Pechtold om samen met de VVD, CDA, D66 en de PvdA in een coalitie te stappen. In plaats daarvan doet hij een beroep op Sybrand Buma van het CDA om open te staan voor formatieonderhandelingen zonder de VVD.
                Dat moet leiden tot een centrum-links kabinet waar niet VVD-leider Mark Rutte, maar Buma de premier wordt.

                "Aansluiten bij het motorblok (VVD, CDA, D66, red.) zou voor iedereen linkse partij neerkomen op politieke zelfmoord", zei Roemer maandag na afloop van zijn gesprek met informateur Edith Schippers.

                Hij herhaalde zijn wens om te komen tot een centrum-links kabinet. Roemer denkt dat voor een aantal partijen deze optie het bespreken waard is, maar begrijpt ook dat Buma de boot op dit moment afhoudt. Volgens Roemer wacht Buma op een vierde partij die het motorblok aan een meerderheid kan helpen.
                              </p>
                              <div class="text-left">
                                <br>
                                <p>Verkoper:     Henk<p>
                                <p>Startbedrag:  €500,-</p>
                                <p>Hoogste bod:  €900,-</p>
                              </div>

                              <div class="input-group" >
                                 <span class="input-group-addon">&euro;</span>
                                 <input style="height:36px;"class="form-control" type="number" required name="price" min="0" value="0" step="any">
                                 <span class="input-group-btn">
                                   <button class="btn btn-orange" type="button"><img src="images/hamerwit.png" class="auction-hammer"></button>
                                 </span>
                               </div>
                            </div>
              </div>
              <div role="tabpanel" class="tab-pane" id="bieden">
                <div class="col-lg-12 timer_row">
                    <h3 id="productCountDown2">COUNTDOWN</h3>
                </div>
                <div class="col-lg-12">
                  <div class="panel-body">
                         <ul class="chat">
                             <li class="left clearfix"><span class="chat-img pull-left">
                                 <img src="http://placehold.it/50/55C1E7/fff&text=JACK" alt="User Avatar" class="img-circle" />
                             </span>
                                 <div class="chat-body clearfix">
                                     <div class="header">
                                         <strong class="primary-font">Jack de Koning</strong> <small class="pull-right text-muted">
                                             <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                                     </div>
                                     <p>
                                         €500.- geboden
                                     </p>
                                 </div>
                             </li>
                             <li class="left clearfix"><span class="chat-img pull-left">
                                  <img src="http://placehold.it/50/55C1E7/fff&text=JACK" alt="User Avatar" class="img-circle" />
                             </span>
                                 <div class="chat-body clearfix">
                                     <div class="header">
                                         <strong class="primary-font">Jack de Koning</strong> <small class="pull-right text-muted">
                                             <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                                     </div>
                                     <p>
                                         €600.- geboden
                                     </p>
                                 </div>
                             </li>
                             <li class="left clearfix"><span class="chat-img pull-left">
                                 <img src="http://placehold.it/50/55C1E7/fff&text=JACK" alt="User Avatar" class="img-circle" />
                             </span>
                                 <div class="chat-body clearfix">
                                     <div class="header">
                                         <strong class="primary-font">Jack de Koning</strong> <small class="pull-right text-muted">
                                             <span class="glyphicon glyphicon-time"></span>12 mins ago</small>
                                     </div>
                                     <p>
                                         €700.- geboden
                                     </p>
                                 </div>
                             </li>
                             <li class="right clearfix"><span class="chat-img pull-right">
                                 <img src="http://placehold.it/50/FA6F57/fff&text=KONING" alt="User Avatar" class="img-circle" />
                             </span>
                                 <div class="chat-body clearfix">
                                     <div class="header">
                                         <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>15 mins ago</small>
                                         <strong class="pull-right primary-font">Koning Arthur</strong>
                                     </div>
                                     <p style="float:right;">
                                         €900.- geboden
                                     </p>
                                 </div>
                             </li>
                         </ul>
                     </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>

</div>
<?php include 'php/includes/footer.php' ?>


  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="bootstrap/assets/js/vendor/jquery.min.js"><\/script>')</script>
  <script src="bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  <script src="bootstrap/assets/js/ie10-viewport-bug-workaround.js"></script>
  <script>
  // Stop carousel
    $('.carousel').carousel({
      interval: false
    });
  </script>
  <script>
  var countDownDate = new Date("May 28, 2017 15:37:25").getTime();

  var x = setInterval(function() {

    var now = new Date().getTime();
    var distance = countDownDate - now;

    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("productCountDown").innerHTML = days + "d " + hours + "h "
    + minutes + "m " + seconds + "s ";
    document.getElementById("productCountDown2").innerHTML = days + "d " + hours + "h "
    + minutes + "m " + seconds + "s ";

    if (distance < 0) {
      clearInterval(x);
      document.getElementById("productCountDown").innerHTML = "EXPIRED";
      document.getElementById("productCountDown2").innerHTML = "EXPIRED";
    }
  }, 1000);
  </script>
</body>
</html>
<?php
$_SESSION['warning'] = null;
?>
