<!DOCTYPE>

<html>
<head>
<?php include "components/head.html"; ?>

</head>

<body>

<div id="googleMap" style="height:600px"></div>


<script src="http://maps.googleapis.com/maps/api/js?libraries=visualization"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
var map,heatmap;
function initialize() {
    var mapOpt = { 
        center:new google.maps.LatLng(38.8833, -100.0167),
        zoom:4,
        mapTypeId:google.maps.MapTypeId.HYBRID
        };

    map = new google.maps.Map(document.getElementById("googleMap"), mapOpt);
}

function createHeatmap(data, status) {
   var dataDoc = $.parseXML(data);
   var $data = $(dataDoc);
   var coordArray = [];
   $data.find("coord").each(function(index){
       coordArray.push(new google.maps.LatLng(Number($(this).find("latitude").text()), 
         Number($(this).find("longitude").text())));
   });
   
   heatmap = new google.maps.visualization.HeatmapLayer({
       data: coordArray,
       map: map
   });  

   heatmap.set('radius', 20);
}

$(document).ready(function() {
    initialize();
   
    $.post("DBRequest.php", {userName:"guest",password:"cashmoney"}, createHeatmap);
});
</script>

</body>
</html>
