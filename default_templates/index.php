<h1>Standaard Templates</h1>
<?php
$files = glob('*.{html,php,js,css}', GLOB_BRACE);
foreach($files as $file) {
  if($file !== 'index.php')
  echo '<li><a href="'.$file.'">'.$file.'</a></li>';
}
?>
