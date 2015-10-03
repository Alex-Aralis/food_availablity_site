<?php
require_once 'safe_db.php';

try{
    $safe_conn = new safe_db('mysql', 'localhost', 'food', $_POST['userName'], $_POST['password']);

    if($_POST['query']){
        error_log("where query input: " . $_POST['query'], 0);
        $safe_conn->sanatize_sql("select latitude,longitude from farmers_markets where " . $_POST['query']);
        $stmt = $safe_conn->execute();
    }else{
        $stmt = $safe_conn->get_conn()->query("select latitude,longitude from farmers_markets");  
    }

    //fetch the data into result lat,lng's into a 2D, numeric array
    $result = $stmt->fetchAll(PDO::FETCH_NUM);
 
    //print data to send
    echo json_encode($result);
}catch(Exception $e){
    error_log("Failure of DBRequest.php to execute sql: " . $e->getMessage(), 0);
}

$safe_conn = null;
?>
