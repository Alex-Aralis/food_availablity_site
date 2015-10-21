<?php
include "/srv/http/food-availability-site/components/userLogin.html";
include "/srv/http/food-availability-site/components/name_and_email.html";


include "/srv/http/food-availability-site/components/load_jquery.html";

echo "<script>";
include "/srv/http/food-availability-site/javascript/jquery.cookie.js";
echo "</script>";

echo "<script>";
include "/srv/http/food-availability-site/javascript/userData.js";
echo "</script>";

echo "<script>";
include "/srv/http/food-availability-site/javascript/userLogin.js";
echo "</script>";

echo "<script>";
include "/srv/http/food-availability-site/javascript/name_and_email.js";
echo "</script>";
?>
