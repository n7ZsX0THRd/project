<?PHP
/*
  iProject Groep 2
  30-05-2017

  file: bied_geschiedenis.php
  purpose:
  Load auction bid history
*/
session_start();

include_once ('../../php/database.php');
include_once ('../../php/user.php');
pdo_connect();

$resultVoorwerp = null;

// Function to calculate time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'jaar',
        'm' => 'maand',
        'w' => 'week',
        'd' => 'dag',
        'h' => 'uur',
        'i' => 'minuut',
        's' => 'seconde',
    );
    $stringMV = array(
        'y' => 'jaar',
        'm' => 'maand',
        'w' => 'week',
        'd' => 'dag',
        'h' => 'uur',
        'i' => 'minuut',
        's' => 'seconden',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            if($diff->$k > 1)
            {
              $v = $diff->$k . ' ' . $stringMV[$k];
            }
            else
            {
              $v = $diff->$k . ' ' . $v;
            }
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' geleden' : 'zojuist';
}
function URL_exists($url){
   $headers=get_headers($url);
   return stripos($headers[0],"200 OK")?true:false;
}


if (isset($_GET['voorwerpnummer'])) {
  $voorwerpnummer = htmlspecialchars($_GET['voorwerpnummer']);

  $bidHistoryQuery = $db->prepare("SELECT * FROM
    (
	SELECT TOP 10
        b.voorwerpnummer,
        b.bodbedrag,
        b.boddagtijd,
        g.gebruikersnaam,
        g.bestandsnaam
      FROM Bod b
  	   LEFT JOIN
  		   Gebruikers g
  			    ON g.gebruikersnaam = b.gebruiker
      WHERE voorwerpnummer = ?
	  ORDER BY b.bodbedrag DESC
	 ) as S
ORDER BY s.boddagtijd DESC");
  $bidHistoryQuery->execute(array($voorwerpnummer));

}

else {
    // Geen voorwerpnummer opgegeven, redirect index.php
    header("Location: ../../index.php");
}




?>

         <ul class="chat">
            <?php

              $loggedInUser = '';
              // Get current loggedIn user username
              if(isUserLoggedIn($db))
                $loggedInUser = getLoggedInUser($db)['gebruikersnaam'];

              //echo $loggedInUser;
              $index = 0;
              // Loop over bidHistory
              foreach($bidHistoryQuery->fetchAll() as $row){
                $index++;
                $profileImage = "http://iproject2.icasites.nl/images/users/".$row['bestandsnaam'];
                // If username is equal to currently logged in user
                // show user on the right
                if($row['gebruikersnaam'] == $loggedInUser){
                ?>
                <li class="right clearfix"><span class="chat-img pull-right">
                  <?php
                  if(URL_exists($profileImage) == true) {
                    ?>
                    <img width="50" height="50" style="background-image:url('<?php echo $profileImage; ?>');background-size:cover;" class="img-circle" />
                    <?php
                  }else {
                    ?>
                    <img width="50" height="50" style="background-image:url('http://iproject2.icasites.nl/images/users/geenfoto/geenfoto.png');background-size:cover;" class="img-circle" />
                    <?php
                  }?>
                </span>
                    <div class="chat-body clearfix">
                        <div class="header">
                            <small class=" text-muted"><span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                            <strong class="pull-right primary-font"><?php echo $row['gebruikersnaam']; ?></strong>
                        </div>
                        <p style="float:right;">
                             &euro;<?php echo number_format($row['bodbedrag'], 2, ',', '.'); ?> geboden
                        </p>
                    </div>
                </li>
                <?php
              }else {
                ?>
                <li class="left clearfix"><span class="chat-img pull-left">
                  <?php
                  if(URL_exists($profileImage) == true) {
                    ?>
                    <img width="50" height="50" style="background-image:url('<?php echo $profileImage; ?>');background-size:cover;" class="img-circle" />
                    <?php
                  }else {
                    ?>
                    <img width="50" height="50" style="background-image:url('http://iproject2.icasites.nl/images/users/geenfoto/geenfoto.png');background-size:cover;" class="img-circle" />
                    <?php
                  }?>
                 </span>
                     <div class="chat-body clearfix">
                         <div class="header">
                             <strong class="primary-font"><?php echo $row['gebruikersnaam']; ?></strong> <small class="pull-right text-muted">
                                 <span class="glyphicon glyphicon-time"></span><?php echo time_elapsed_string($row['boddagtijd']); ?></small>
                         </div>
                         <p>
                            &euro;<?php echo number_format($row['bodbedrag'], 2, ',', '.'); ?> geboden
                         </p>
                     </div>
                 </li>
                <?php
              }
            }
            // Show message if bid count == 0
            if($index == 0)
            {
              ?>
              <p class="bg-warning" style="padding:5px;margin-top:15px;">
                  Er is nog niet geboden op deze veiling.<br>
              </p>
              <?php
            }
            ?>
         </ul>
