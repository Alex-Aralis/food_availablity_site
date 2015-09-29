<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=food", 'guest', 'cashmoney');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    //select the column names for farmers market
    $query = $conn->query("select column_name, data_type from information_schema.columns where table_name='farmers_markets'");

    $column_names = $query->fetchAll(PDO::FETCH_NUM);
    
    //loop through the column names in farmers_markets creating <details> elements
    //<details> only works in chrome and safari currently
    foreach ($column_names as $row){
        $name = $row[0];
        $type = $row[1];
        
        //only offer reasonable options
        if($type === "char" || $type === "enum" || $type === "boolean" || $name === "locale"){ 
            //cant prepare non-values ):
            $query = $conn->query("select count(*) as number,$name from farmers_markets group by $name");
          
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            echo "<details>";
                echo "<summary> $name </summary>";//title of the details drop down
                //loop through the groups of a column creating checkboxes
                foreach ($results as $result){
                    //replace flasy groups with "unknown"
                    echo "<input type='checkbox' class='options' name='$name' value='" . ($result[$name] ? $result[$name] : "unknown")  . "'>";
                    echo ($result[$name] ? $result[$name] : "unknown") . " ({$result['number']}) <br>";
                    echo "</input>";
                } 
            echo "</details>";
        }
    }

}catch (PDOException $e){
   error_log("Error in option creation: " . $e->getMessage(), 0);
}

$conn = null;
?>
