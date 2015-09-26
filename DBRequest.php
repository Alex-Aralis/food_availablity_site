<?php

$error_file = "/srv/http/food-availability-site/logs/php.log";

try {
    $conn = new PDO("mysql:host=localhost;dbname=food", $_POST['userName'], 
         $_POST['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//  $conn->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);  already the default?
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //returns fetches
         //as correct types

    if(array_key_exists("query", $_POST) && $_POST['query']!=''){
        $stmt = $conn->query(
             "select latitude,longitude from farmers_markets where {$_POST['query']}");
        error_log("query: {$_POST['query']}\n", 3, $error_file);
    }else{
        $stmt = $conn->query("select latitude,longitude from farmers_markets");
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    echo json_encode($result);

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage(), 3, $error_file);
}

$conn = null;
?>
