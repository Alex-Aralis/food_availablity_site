<!DOCTYPE html>

<html>

<head>
    <?php include "/srv/http/food-availability-site/components/head.html"; ?>
</head>

<body>

<?php include "/srv/http/food-availability-site/components/menubar.html"; ?>


<div class="leftsidebar" >
    <button id="searchbutton" style="margin:auto; display:block">Search</button>
    <?php include "/srv/http/food-availability-site/components/options.php"; ?>
</div>

<div class="center">
    <?php include "/srv/http/food-availability-site/components/map.html"; ?>
</div>

<!-- google maps api with visualization libs for the heatmaps -->
<script src="http://maps.googleapis.com/maps/api/js?libraries=visualization"></script>

<!-- jQuery for stuff and things -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<!-- script to initialize and link in the googlemap with the search options 
     Depends on: googlemaps(with visualization), JQuery-->
<script src="/javascript/map.js"></script>
</body>
</html>
