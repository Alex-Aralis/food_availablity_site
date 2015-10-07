var map,heatmap;

function initMap() {
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
    
    setMapTitle(length);
     
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

function setMapTitle(coordsCardinality){
    $("h2.mapHeader").text("(" + coordsCardinality + ")");
}

function initHeatmap(sql){
    $.post("/php/DBRequest.php", {userName:"guest",password:"cashmoney", sql:sql}, function(data, status){
        createHeatmap(createHeatmapArray(data));
    }); 

}

function updateHeatmap(sql){
    $.post("/php/DBRequest.php", {userName:"guest",password:"cashmoney",sql:sql}, function(data, status){
        heatmap.setData(createHeatmapArray(data));
    });
}

//makes a string with checkedName,=,checkedValue pairs
function getCheckedValues(){
    var whereblob = "";
    //iterate through checked checkboxes in the options panel.
    $("input:checked.options").each(function(index, elem){
        whereblob += $(elem).attr("name") + "='" + $(elem).val() + "' AND ";
        console.log(whereblob);
    });
 
    //removing extraneous ' AND ' from the end of whereblob
    if(whereblob !== ''){
        whereblob = whereblob.substring(0, whereblob.length - 5);
        console.log(whereblob);
    }

    return whereblob;
}

$(document).ready(function() {
    //initialize googlemap
    initMap();
     
    //initialize heatmap and submit it to googlemap
    initHeatmap("select latitude,longitude from farmers_markets");
  
    //set handler for searchbutton
    $("#searchbutton").click(function(event){
        updateHeatmap("select latitude, longitude from farmers_markets where " + getCheckedValues());
    });
    
    //update heatmap if checkbox is changed.
    $("input:checkbox.options").change(function (){
        var whereblob = getCheckedValues();
            updateHeatmap("select latitude, longitude from farmers_markets where " + getCheckedValues());
        if(whereblob === ''){ 
            updateHeatmap("select latitude, longitude from farmers_markets");
        }else{

        }
    });
        
    //if enter is pressed in the searchbox, press the searchbutton
    $(document).keyup(function(event){
        if(event.keyCode == 13){
            updateHeatmap(getCheckedValues());
        }
    });
});

