<?php

try {
//    $conn = new PDO("mysql:host=localhost;dbname=food", $_POST['userName'], $_POST['password']);
    $conn = new PDO("mysql:host=localhost;dbname=food", "guest", "cashmoney");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("select latitude,longitude from farmers_markets");
    $stmt->execute();
    
    $result = $stmt->fetchAll(); 
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
    echo "</heatmap>";

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage(), 3, "/srv/www/logs/php.log");
}

$conn = null;
?>
