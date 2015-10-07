<?php
require_once 'safe_db.php';

try{
    $safe_conn = new safe_db('mysql', 'localhost', 'food', $_POST['userName'], $_POST['password']);

    //setting PDO result fetching method.
    if(isset($_POST['fetch_method'])){
        switch($_POST['fetch_method']){
            case 'FETCH_NUM':
                $fetch_method = PDO::FETCH_NUM;
                break;
            default :
                error_log("Fetch method from client not valid: " . 
                    $_POST['fetch_method'] . "setting to default FETCH_NUM");
                $fetch_method = PDO::FETCH_NUM;           
        }
        //set client sent fetch method
    }else{
        //if nothing sent, using default fetch method
        $fetch_method = PDO::FETCH_NUM;
    }
    
    if(isset($_POST['sql'])){
        error_log("sql input: " . $_POST['query'], 0);
        $safe_conn->sanitize_sql($_POST['sql']);
        $stmt = $safe_conn->execute();
    }else{
        error_log("No sql statement sent: exiting...");
        exit(1);
    }

    //fetch the data using $fetch_method
    $result = $stmt->fetchAll($fetch_method);
 
    //print data to send
    echo json_encode($result);
}catch(Exception $e){
    error_log("Failure of DBRequest.php to execute sql: " . $e->getMessage(), 0);
    echo json_encode(null);
}

$safe_conn = null;
?>
