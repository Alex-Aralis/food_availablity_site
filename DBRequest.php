<?php

try {
    $conn = new PDO("mysql:host=localhost;dbname=food", $_POST['userName'], $_POST['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(array_key_exists("query", $_POST)){
        $stmt = $conn->query("select latitude,longitude from farmers_markets where {$_POST['query']}");
        //$stmt = $conn->prepare("select latitude,longitude from farmers_markets where coffee='Y'");
        error_log("query: {$_POST['query']}\n", 3, "/srv/www/logs/php.log");
        //$stmt->bindParam(':query', $_POST['query']);
    }else{
        $stmt = $conn->query("select latitude,longitude from farmers_markets");
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    echo json_encode($result);
    
/*
    $len = count($result);    
    echo "<heatmap>";
    for($i = 0; $i < $len; $i++){
        echo "<coord>";
            foreach($result[$i] as $key => $value){
                if("string" == gettype($key)){
                    echo "<$key>$value</$key>";
                }
            }
        echo "</coord>";
    } 
    echo "</heatmap>"; */

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage(), 3, "/srv/www/logs/php.log");
}

$conn = null;
?>
