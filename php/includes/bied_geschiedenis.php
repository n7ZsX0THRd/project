<?PHP
session_start();

include ('../../php/database.php');
include ('../../php/user.php');
pdo_connect();

$resultVoorwerp = null;


function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

if (isset($_GET['voorwerpnummer'])) {
  $voorwerpnummer = htmlspecialchars($_GET['voorwerpnummer']);

  $data = $db->prepare("SELECT
v.voorwerpnummer,
  v.titel,
  v.beschrijving,
  v.startprijs,
  v.betalingswijze,
  v.betalingsinstructie,
  v.postcode,
  v.plaatsnaam,
  v.land,
  v.looptijd,
  v.looptijdbegin,
  v.verzendkosten,
  v.verzendinstructie,
  v.verkoper,
  v.koper,
  v.looptijdeinde,
  v.veilinggesloten,
  v.verkoopprijs,
  vir.rubrieknummer as rn,
  l.lnd_Landnaam as landNaam,
  dbo.fnGetMinBid(v.voorwerpnummer) AS minimaalBod,
  dbo.fnGetHoogsteBod(v.voorwerpnummer) AS hoogsteBod
  FROM Voorwerp v
  JOIN VoorwerpInRubriek vir
    ON vir.voorwerpnummer = v.voorwerpnummer
  JOIN Landen l
    ON l.lnd_Code = v.land
    WHERE v.voorwerpnummer=?");
  $data->execute([$voorwerpnummer]);

  $resultVoorwerplist=$data->fetchAll();

  if(count($resultVoorwerplist) === 0){
    header("Location: index.php"); // voorwerpnummer ongeldig
  }
  else {
    $resultVoorwerp = $resultVoorwerplist[0];

    $data2 = $db->prepare("SELECT TOP 3 v.voorwerpnummer, titel, looptijdeinde, Foto.bestandsnaam
                           FROM Voorwerp v
                           CROSS APPLY
                           (
                               SELECT  TOP 1 Bestand.bestandsnaam
                               FROM    Bestand
                               WHERE   Bestand.voorwerpnummer = v.voorwerpnummer
                           ) Foto
                           WHERE verkoper = ?
                           AND v.voorwerpnummer != ?
                           AND v.looptijdeinde > GETDATE()
                           ");
    $data2->execute(array($resultVoorwerp['verkoper'],$resultVoorwerp['voorwerpnummer']));
    $meerVanVerkoper = $data2->fetchAll();

    $rubriek = $resultVoorwerp['rn'];

    $data = $db->prepare("
SELECT TOP 4 bestandsnaam FROM Bestand b WHERE b.voorwerpnummer = ? ");
    $data->execute([$voorwerpnummer]);

    $resultImages=$data->fetchAll();

  }

}

else {
    // Geen voorwerpnummer opgegeven, redirect index.php
    header("Location: ../../index.php");
}


$bidHistoryQuery = $db->prepare("SELECT b.voorwerpnummer,b.bodbedrag,b.boddagtijd,g.gebruikersnaam,g.bestandsnaam FROM Bod b
	JOIN
		Gebruikers g
			ON g.gebruikersnaam = b.gebruiker
WHERE voorwerpnummer = ? ORDER BY boddagtijd ASC");
$bidHistoryQuery->execute(array($resultVoorwerp['voorwerpnummer']));

?>
<div class="col-lg-12">
  <div class="panel-body">
         <ul class="chat">
            <?php

              $loggedInUser = '';

              if(isUserLoggedIn($db))
                $loggedInUser = getLoggedInUser($db)['gebruikersnaam'];

              //echo $loggedInUser;

              foreach($bidHistoryQuery->fetchAll() as $row){

                if($row['gebruikersnaam'] == $loggedInUser){
                ?>
                <li class="right clearfix"><span class="chat-img pull-right">
                  <?php
                  if(file_exists('images/users/'.$row['bestandsnaam'])) {
                    ?>
                    <img width="50" height="50" style="background-image:url(images/users/<?php echo $row['bestandsnaam']; ?>);background-size:contain;" class="img-circle" />
                    <?php
                  }else {
                    ?>
                    <img width="50" height="50" style="background-image:url(images/users/geenfoto/geenfoto.png);background-size:contain;" class="img-circle" />
                    <?php
                  }?>
                </span>
                    <div class="chat-body clearfix">
                        <div class="header">
                            <small class=" text-muted"><span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                            <strong class="pull-right primary-font"><?php echo $row['gebruikersnaam']; ?></strong>
                        </div>
                        <p style="float:right;">
                             &euro;<?php echo $row['bodbedrag']; ?> geboden
                        </p>
                    </div>
                </li>
                <?php
              }else {
                ?>
                <li class="left clearfix"><span class="chat-img pull-left">
                  <?php
                  if(file_exists('images/users/'.$row['bestandsnaam'])) {
                    ?>
                    <img width="50" height="50" style="background-image:url(images/users/<?php echo $row['bestandsnaam']; ?>);background-size:contain;" class="img-circle" />
                    <?php
                  }else {
                    ?>
                    <img width="50" height="50" style="background-image:url(images/users/geenfoto/geenfoto.png);background-size:contain;" class="img-circle" />
                    <?php
                  }?>
                 </span>
                     <div class="chat-body clearfix">
                         <div class="header">
                             <strong class="primary-font"><?php echo $row['gebruikersnaam']; ?></strong> <small class="pull-right text-muted">
                                 <span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                         </div>
                         <p>
                            &euro;<?php echo $row['bodbedrag']; ?> geboden
                         </p>
                     </div>
                 </li>
                <?php
              }
            }
            ?>
         </ul>
     </div>
</div>
