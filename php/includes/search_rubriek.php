<?php
/*
  iProject Groep 2
  30-05-2017

  file: search_rubriek.php
  purpose:
    Search rubriek, used in 'veilingtoevoegen.php' to search for a rubriek
*/
session_start();

include_once ('../../php/database.php');
include_once ('../../php/user.php');
pdo_connect();
// Connect to database

// Check if search isset,
if (isset($_GET['search'])) {
  $searchInput = $_GET['search'];

  // Query to search rubriek
  $searchRubriekQuery = $db->prepare("SELECT
	r4.rubrieknaam AS parentparent,
	r3.rubrieknaam AS parent,
	r.rubrieknaam AS child,
	r.rubrieknummer AS childID
FROM Rubriek r
LEFT JOIN
	Rubriek r3
	ON r3.rubrieknummer = r.parentRubriek
LEFT JOIN
	Rubriek r4
	ON r4.rubrieknummer = r3.parentRubriek
WHERE
	r.rubrieknummer NOT IN(
		SELECT
			r2.parentRubriek
		FROM
			Rubriek r2
		WHERE
			r2.parentRubriek IS NOT NULL
	)
	AND
	(
		r.rubrieknaam LIKE ?
		OR
		r3.rubrieknaam LIKE ?
	)
  AND
    r.inactief = 0
ORDER BY
	parentparent ASC,
	parent ASC,
	child ASC");
  $searchRubriekQuery->execute(array('%'.$searchInput.'%','%'.$searchInput.'%'));

  $result = $searchRubriekQuery->fetchAll();
}
else {
    exit();
}

  echo '<p></p>';
  echo '<ul class="rubriek_search">';
  $countedResults = 0;
  // Loop over found rubrieken
  foreach($result as $row)
  {
    if(isset($row['parentparent']) && $row['parentparent']  == 'Root')
      $row['parentparent'] = "Rubrieken";
    if(isset($row['parent']) && $row['parent']  == 'Root')
      $row['parent'] = "Rubrieken";
    ?>
      <li class="rubriek_search" data-dismiss="modal" id="add_rubriek" data-parentparentnaam="<?php echo $row['parentparent']?>" data-parentnaam="<?php echo $row['parent']?>" data-rubrieknaam="<?php echo $row['child']?>"  data-rubriekid="<?php echo $row['childID']?>">
        <?php
        if(isset($row['parentparent']) && $row['parentparent'] !== null)
        {
          echo $row['parentparent'].' '.'<span class="glyphicon glyphicon-menu-right"></span>';
        }
        if(isset($row['parent']) && $row['parent'] !== null)
        {
          echo $row['parent'].' '.'<span class="glyphicon glyphicon-menu-right"></span>';
        }
        ?>
        <?php echo $row['child'];?>
      </li>
    <?php
    $countedResults++;
  }
  echo '</ul>';
  // Check if rubriek found, otherwise show notifcation
  if($countedResults == 0)
  {
    echo '<p class="bg-warning" style="padding:5px;margin-top:10px;">Geen rubrieken gevonden</p>';
  }

?>
