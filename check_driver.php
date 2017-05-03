<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
echo '<pre>';
print_r(PDO::getAvailableDrivers());
echo '</pre>';
?>
