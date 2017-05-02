<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="bootstrap/favicon.ico">

    <title>Normal Page</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="bootstrap/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="bootstrap/assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link href="stylesheet.css" rel="stylesheet">
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top header-style">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><img src="images/logo.png" width="80">Eenmaal Andermaal</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <?php include 'php/includes/menu.php' ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12 home-header">
          <h1>Eenmaal Andermaal</h1>
          <p>Welkom op de beste veilingsite van Nederland</p>
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-lg-2 col-sm-4 sidebar">
          <h3>Categorieën</h3>
          <ul class="menubar">
            <li>
              <a href="">Menu Item</a>
            </li>
            <li class="toggle-sub">
              <a href="">Menu Item</a>
            </li>
            <ul class="sub">
              <li>
                <a href="">Sub Item</a>
              </li>
              <li>
                <a href="">Sub Item</a>
              </li>
            </ul>
            <li class="toggle-sub">
              <a href="">Menu Item</a>
            </li>
            <ul class="sub">
              <li>
                <a href="">Sub Item</a>
              </li>
              <li>
                <a href="">Sub Item</a>
              </li>
            </ul>
            <li>
              <a href="">Menu Item</a>
            </li>
          </ul>
        </div>
        <div class="col-md-8 col-lg-10 col-sm-8">
          <div class="container-fluid content_col">

            <div class="row">
              <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="container-fixed">
                  <div class="row">
                    <div class="col-lg-4">
                      <h1>Voorbeeld</h1>
                      <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                      </p>
                    </div>
                    <div class="col-lg-4">
                      <h1>Voorbeeld</h1>
                      <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                      </p>
                    </div>
                    <div class="col-lg-4">
                      <h1>Voorbeeld</h1>
                      <p>
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 remove-margin">
                <h1>Voorbeeld</h1>
              <div>
              <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <div class="container-fixed">
                  <div class="row">
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4">
                      <div class="item-card">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-xs-12">
                              <h2>Vliegtuig</h2>
                              <img src="images/vliegtuig.png">
                            </div>
                            <div class="col-xs-12">
                              <div class="text-right">
                                <div class="btn-group card-options" role="group" aria-label="...">
                                  <button type="button" class="btn btn-niagara"><span class="glyphicon glyphicon-heart"></span></button>
                                  <button type="button" class="btn btn-orange">Bekijk</button>
                                </div>
                                <div class="input-group input-group-lg" style="margin-top:5px;">
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                                  <input type="text" class="form-control" aria-label="Amount (to the nearest dollar)">
                                  <span class="input-group-addon">,-</span>
                                  <span class="input-group-btn">
                                    <button class="btn btn-orange" type="button"><img src="images/hamerwit.png" class="auction-hammer"></button>
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 remove-margin">
                <h1>Voorbeeld</h1>
                <p>
                  "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
                <p>
                  "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
                <p>
                  "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
                <p>
                  "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
                </p>
              <div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
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
