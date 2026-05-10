<?php
$f = 'file.php';
echo basename($f) . PHP_EOL;
$ext = pathinfo($f, PATHINFO_EXTENSION);
echo $ext . PHP_EOL;
?>