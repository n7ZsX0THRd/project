
<div class="container">
  <div class="row">
    <div class="col-md-4 col-lg-2 col-sm-4 sidebar">
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
        </ul>
      </ul>
    </div>
    <div class="col-md-8 col-lg-10 col-sm-8">
      <div class="container-fluid  content_col">
        <div class="row navigation-row">
            <p>
              <a href="">
                <span class="glyphicon glyphicon-home "></span>
              </a>
              <span class="glyphicon glyphicon-menu-right"></span>
              <a href="">Mijn Account</a>
              <span class="glyphicon glyphicon-menu-right"></span>
              <a href="">Instellingen</a>
            </p>
        </div>
        <div class="row content_top_offset">
          <div class="col-lg-6">
            <form>
              <div class="form-group">
                <label for="exampleInputEmail1">Gebruikersnaam</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-5">
                    <label for="exampleInputEmail1">Voornaam</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Voornaam">
                  </div>
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Achternaam</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Achternaam">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Voornaam</label>
                    <select class="form-control">
                      <option selected disabled>Dag</option>
                      <?php for ($i = 1; $i <= 31; $i++) { ?>
                          <option><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Achternaam</label>
                    <select class="form-control">
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
                  </div>
                  <div class="col-lg-4">
                    <label for="exampleInputEmail1">Achternaam</label>
                    <select class="form-control">
                      <option selected disabled>Jaar</option>
                      <?php for ($i = date("Y"); $i >= 1900; $i--) { ?>
                          <option><?php echo $i; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-7">
                    <label for="exampleInputEmail1">Adres</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Adres">
                  </div>
                  <div class="col-lg-2">
                    <label for="exampleInputEmail1">Nr.</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nr.">
                  </div>
                  <div class="col-lg-3">
                      <label for="exampleInputEmail1">Toev.</label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Toev.">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Voornaam</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
              </div>
              <div class="form-group">
                <label for="exampleInputFile">File input</label>
                <input type="file" id="exampleInputFile">
                <p class="help-block">Example block-level help text here.</p>
              </div>
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Check me out
                </label>
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
          </div>
          <div class="col-lg-6">

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'php/includes/footer.php' ?>
