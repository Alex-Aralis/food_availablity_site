<!doctype html>

<html>
<head>
<?php include "components/head.html"; ?>

</head>

<body>

<input type="text" id="searchbox"></input>
<button id="searchbutton" onclick="search()" >Search</button>
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



function createHeatmapArray(data){
    console.log(data);
    var dataArray = JSON.parse(data);
    length = dataArray.length;
    var coordArray = [];
   
    //create array of google.maps.LatLng opbjects, ommiting the null entries 
    for(var i = 0; i < length; i++){
        if(dataArray[i][0] !== null  && dataArray[i][1] !== null) 
        coordArray.push(new google.maps.LatLng(
            dataArray[i][0],  //lat
            dataArray[i][1]));//lng
    }

    return coordArray;
}

function createHeatmap(heatmapArray) {
    heatmap = new google.maps.visualization.HeatmapLayer({
        data: heatmapArray,
        map: map
    });  

    //heatmap.set('radius', 1);
    //heatmap.set('dissipating', false);
}

function updateHeatmap(query){
    $.post("DBRequest.php", {userName:"guest",password:"cashmoney",query:query}, function(data, status){
        console.log(data);
        heatmap.setData(createHeatmapArray(data));
    });
}

$(document).ready(function() {
    initialize();
    
    $.post("DBRequest.php", {userName:"guest",password:"cashmoney"}, function(data, status){
        createHeatmap(createHeatmapArray(data));
    });
    
});

//if enter is pressed in the searchbox, press the searchbutton
$("#searchbox").keyup(function(event){
    if(event.keyCode == 13){
        $("#searchbutton").click();
    }
});

function search(){
    console.log($("#searchbox").val());
    updateHeatmap($("#searchbox").val());
}
</script>

</body>
</html>
