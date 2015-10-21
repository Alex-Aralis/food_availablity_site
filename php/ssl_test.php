<?php


$random_bytes = openssl_random_pseudo_bytes(32,$strong);

var_dump(bin2hex($random_bytes));
var_dump($strong);
?>
