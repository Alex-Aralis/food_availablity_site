<?php

$error_file = "/srv/http/food-availability-site/logs/php.log";

try {
    $conn = new PDO("mysql:host=localhost;dbname=food", $_POST['userName'], 
         $_POST['password']);

    //setting up the exeption handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //returns fetches as correct type if the mysql type exists in php
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
   
    //gets the column names from farmers market
    $stmt = $conn->query(
        "select column_name from information_schema.columns where table_name='farmers_markets'");

    //creates an array of all column names for the table farmers_market
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);


    //produces an accociative array with keys of valid column names and values of true for all columns.
    //in this way, all a column name can be 'checked' to be valid and therefore safe use in sql statements.
    //currently column name contains ALL columns in the table, not ONLY the ones specified in the
    //options.php component.
    //this validation uses the falsyness of NULL as a failed result.  
    //the array will not return a true false .:
    //      valid_column_names[$invalid_column] !== false, but 
    //      valid_column_names[$invalid_column] == false
    $valid_column_names = [];
    foreach($result as $column_name){ 
        error_log("from dbrequest> " . "valid_column_names construction: $column_name", 0);
        $valid_column_names[$column_name] = true;
    }
  
    //it would cause problems if there where a column_name === "guest" or "password"
    $valid_column_names['guest'] = null;
    $valid_column_names['password'] = null;

    //create WHERE condition text for the prepare sanitized by valid_column_names 
    //and an array to bind to the prepare.
    $prep_value_array = [];
    $prep_query_string = '';
    foreach($_POST as $column_name => $condition){
        error_log("from dbrequest> " . "iterating through _POST: $column_name => $condition", 0);
        if($valid_column_names[$column_name]){
            error_log("from dbrequest> " . "column added!!!: $column_name => $condition", 0);
            $prep_query_string += " $column_name=:$column_name AND ";
            $prep_value_array[':'.$column_name] =  $condition;
        }
    }
    
    error_log("from dbrequest> " . "prep_query_string: $prep_query_string", 0);
    
    //if there were conditions sent
    if($prep_query_string){ 
        //removing the extraneous " AND "
        $prep_query_string = $prep_query_string.substr(0, strlen($prep_query_string) - 5);

        //prepare the query
        $stmt = $conn->prepare("select latitude,longitude from farmers_markets where $prep_query_string");
    
        //bind the variables and execute the statment.
        $stmt->execute($prep_value_array);

    }else{//if there were no conditions do a simple query
        //no user input so no prep, and no need for execute
        $stmt = $conn->query("select latitude,longitude from farmers_markets");
    }
    
    //fetch the data into result lat,lng's into a 2D, numeric array
    $result = $stmt->fetchAll(PDO::FETCH_NUM);
    
    //print data to send
    echo json_encode($result);

    /*
    if(array_key_exists("query", $_POST) && $_POST['query']!=''){
        $query = $_POST['query'];

        //$query = $conn->quote($_POST['query']);
        $stmt = $conn->query(
             "select latitude,longitude from farmers_markets where $query");
        error_log("query: $query\n", 3, $error_file);
    }else{
        $stmt = $conn->query("select latitude,longitude from farmers_markets");
    }
    
    $result = $stmt->fetchAll(PDO::FETCH_NUM); 

    echo json_encode($result);

    */

} catch (PDOException $e) {
    error_log("Error in map.php: " . $e->getMessage(), 0);
}

$conn = null;
?>
