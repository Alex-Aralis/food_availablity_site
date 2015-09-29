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
    //      column_whitelist[$invalid_column] !== false, but 
    //      column_whitelist[$invalid_column] == false
    // will casue a E_NOTICE level output when mapping fails
    $column_whitelist = [];
    foreach($result as $column_name){ 
        error_log("from dbrequest> " . "column_whitelist construction: $column_name", 0);
        $column_whitelist[$column_name] = true;
    }

    //returns true for a subset of the sql comparison operators
    function isBooleanOperator($operator){
        //breaks unneeded because return ends the function
        switch($operator){
            case '=':
            case '<':
            case '>':
            case '<=':
            case '>=': 
            case '!=':
                return true;
            default:
                return false;
        }
    }

    //explode _POST['query'] into conditional chunks seperated by spaces
    $raw_query_conditions = explode(' ', $_POST['query']);

    //create WHERE condition text for the prepare sanitized by column_whitelist 
    //and an array to bind to the prepare.
    $prep_query_string = '';

    //create a non-hashed array (only int ref's allowed)
    $raw_condition_segments = new SplFixedArray(3);

    //array that will hold 'bindpoint' => variable mapping
    $prep_value_array = [];
 
    //create
    foreach($raw_query_conditions as $raw_condition){
        error_log("from dbrequest> " . "iterating through conditions: $raw_condition", 0);
        
        //break apart comma seperated conditional parts
        //[0] => column name
        //[1] => conditional operator
        //[2] => value
        $raw_condition_segments = explode(',', $raw_condition);
        if($column_whitelist[$raw_condition_segments[0]] && isBooleanOperator($raw_condition_segments[1])){
            error_log("from dbrequest> " . "column added!!!: {$raw_condition_segments[0]}", 0);
            $prep_query_string .= $raw_condition_segments[0] . 
                $raw_condition_segments[1] .":" . $raw_condition_segments[0] . " AND ";
            $prep_value_array[':'.$raw_condition_segments[0]] =  $raw_condition_segments[2];
        }
    }
   
    //$raw_condition_segments = null;
    

    error_log("from dbrequest> " . "prep_query_string: $prep_query_string", 0);
    
    //if _POST['query'] had any valid conditions
    if($prep_query_string){ 
        //removing the extraneous " AND "
        $prep_query_string = substr($prep_query_string, 0, strlen($prep_query_string) - 5);

        error_log("from dbrequest> " . "prep_query_string before prepare: $prep_query_string", 0);

        //prepare the query
        $stmt = $conn->prepare("select latitude,longitude from farmers_markets where $prep_query_string");
    
        //bind the variables and execute the statment.
        $stmt->execute($prep_value_array);

    }else{//if there were no 
        //no user input so no prep, execute proformed implicetly by query
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
    error_log("Error in DBRequest PDO: " . $e->getMessage(), 0);
}

$conn = null;
?>
