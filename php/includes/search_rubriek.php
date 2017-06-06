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


if (isset($_GET['search'])) {
  $searchInput = $_GET['search'];

  $searchRubriekQuery = $db->prepare("SELECT DISTINCT TOP 10
  	r.rubrieknummer AS parentId,
  	r.rubrieknaam AS parent,
  	r4.rubrieknaam AS child,
  	r4.rubrieknummer AS childId
  FROM
  	Rubriek r
  	JOIN Rubriek r4
  		ON r4.parentRubriek = r.rubrieknummer
  	WHERE r.rubrieknummer IN(
  	SELECT
  		r2.rubrieknummer
  	FROM
  		Rubriek r2
  	RIGHT JOIN
  		Rubriek r3
  		ON r2.rubrieknummer = r3.parentRubriek
  	WHERE r2.parentRubriek IS NOT NULL
  )
  AND r4.rubrieknaam LIKE ?
  ORDER BY r.rubrieknaam");
  $searchRubriekQuery->execute(array('%'.$searchInput.'%'));

  $result = $searchRubriekQuery->fetchAll();
}
else {
    exit();
}
echo '<p></p>';
if(count($result) > 0)
{
  echo '<ul class="rubriek_search">';
  foreach($result as $row)
  {
    ?>
      <li class="rubriek_search" data-dismiss="modal" id="add_rubriek" data-parentnaam="<?php echo $row['parent']?>" data-rubrieknaam="<?php echo $row['child']?>"  data-rubriekid="<?php echo $row['childId']?>"><?php echo $row['parent'];?> <span class="glyphicon glyphicon-menu-right"></span> <?php echo $row['child'];?></li>
    <?php
  }
  echo '</ul>';
}
else {
  ?>
    <p class="bg-warning" style="padding:5px;margin-top:10px;">Geen rubrieken gevonden</p>
  <?php
}

?>
