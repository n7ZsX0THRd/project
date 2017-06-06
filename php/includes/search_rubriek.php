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

}
else {
    exit();
}
echo '<ul>';
foreach($searchRubriekQuery->fetchAll() as $row)
{
  ?>
    <li><?php echo $row['parent'];?> > <?php echo $row['child'];?></li>
  <?php
}
echo '</ul>';


?>
