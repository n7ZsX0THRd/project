
    <div class="container">
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 loginscherm">
          <h1>Registreren</h1>
          <p>Welkom op de beste veilingsite van Nederland</p>

            <div>
            <form class="form-horizontal">



            <div class="login">

                  <div class="input-group">
                      <div class="input-group-addon "><span class="glyphicon glyphicon-user" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="text" class="form-control" id="Tel" placeholder="Gebruikersnaam">
                      <input type="text" class="form-control" id="Voornaam" placeholder="Voornaam">
                      <input type="text" class="form-control" id="Achternaam" placeholder="Achternaam">
                      <!--<input type="text" style="max-width:30%" class="form-control" id="Datum" placeholder="Dag">-->
                      <select class="form-control" style="max-width:30%">
                        <option selected disabled>Dag</option>
                        <?php for ($i = 1; $i <= 31; $i++) { ?>
                            <option><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                      <select class="form-control" style="max-width:30%">
                        <option selected disabled>Maand</option>
                        <?php
                        $months = array("januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december");
                        foreach ($months as &$value)
                        {
                          ?>
                            <option><?php echo $value; ?></option>
                        <?php
                        }
                        ?>
                      </select>
                      <select class="form-control" style="max-width:40%">
                        <option selected disabled>Jaar</option>
                        <?php for ($i = date("Y"); $i >= 1900; $i--) { ?>
                            <option><?php echo $i; ?></option>
                        <?php } ?>
                      </select>
                      <!--<input type="text" style="max-width:30%" class="form-control" id="Datum" placeholder="Maand">
                      <input type="text" style="max-width:40%" class="form-control" id="Datum" placeholder="Jaar">-->

                  </div>


                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></div>
                      <input style="max-width:58%" type="adres" class="form-control" id="Adres" placeholder="Adres">
                      <input style="max-width:20%" type="Number" class="form-control" id="Nummer" placeholder="Nr.">
                      <input style="max-width:22%" type="text" class="form-control" id="Nummer" placeholder="Toev.">
                      <input type="" class="form-control" id="Postcode" placeholder="Postcode" pattern="[1-9][0-9]{3}\s?[a-zA-Z]{2}">
                  </div>

                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></div>
                      <input type="" class="form-control" id="Tel" placeholder="Telefoonnummer">
                  </div>

                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true" background="#f0f0f0"></span></div>
                      <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                      <input type="email" class="form-control" id="inputEmail" placeholder="Bevestig email">
                  </div>

                  <div class="input-group">
                      <div class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>
                      <input type="password" class="form-control" id="inputPassword" placeholder="Wachtwoord">
                    <input type="password" class="form-control" id="inputPassword" placeholder="Bevestig wachtwoord">
                  </div>




            </div>

              <div class="bevestig">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="position:relative;">
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <button type="submit" class="btn btn-orange align-right" >Registreer</button>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <p class="sub-text-register">Al een account? <a href="index.php?page=login">Log dan hier in</a></p>
                </div>
              </div>
            </form>
            </div>
            </div>
      </div>

    </div>
