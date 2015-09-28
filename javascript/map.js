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
    //console.log(data);
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
    $.post("/php/DBRequest.php", {userName:"guest",password:"cashmoney",query:query}, function(data, status){
        console.log(data);
        heatmap.setData(createHeatmapArray(data));
    });
}

//makes a string with checkedName=checkedValue pairs
function getCheckedValues(){
    var queryblob = "";
    //iterate through checked checkboxes in the options panel.
    $("input:checked.options").each(function(index, elem){
        queryblob += " " + $(elem).attr("name") + "='" + $(elem).val() + "' AND";
        console.log(queryblob);
    });
 
    //removing extraneous ' AND' from the end of queryblob
    queryblob = queryblob.substring(0, queryblob.length - 4);
    console.log(queryblob);
    return queryblob;
}

$(document).ready(function() {
    initialize();
    
    $.post("/php/DBRequest.php", {userName:"guest",password:"cashmoney"}, function(data, status){
        createHeatmap(createHeatmapArray(data));
    });
   
    //set handler for searchbutton
    $("#searchbutton").click(function(event){
        updateHeatmap(getCheckedValues());
    });
    
    //if enter is pressed in the searchbox, press the searchbutton
    $(document).keyup(function(event){
        if(event.keyCode == 13){
            updateHeatmap(getCheckedValues());
        }
    });
});



function search(query){
    updateHeatmap(query);
}
