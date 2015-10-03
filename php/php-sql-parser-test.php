<?php
#require_once '/srv/http/food-availability-site/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLParser.php';
#require_once '/srv/http/food-availability-site/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLCreator.php';
require_once 'safe_db.php';

if($argv[1]){ //value to be parsed is first command line argument
    $sql = $argv[1];
}else{ //default value for $sql
    $sql = "SELECT flavor,color FROM fooditem WHERE flavor.intensity > 5 AND color.orange='no'";
}


$safe_conn = new safe_db('mysql', 'localhost', 'food', 'guest', 'cashmoney');

try{
    echo "Prunings: " . $safe_conn->sanatize_sql($sql) . "\n";
    echo "======================================New Tree=========================================\n";
    print_r($safe_conn->get_safe_parsed_sql());
    echo "=====================================New Tree End======================================\n";
    echo "Sanatized SQL: " . $safe_conn->get_sanatized_sql() . "\n";

}catch(Exception $e){
   echo "safe_conn->sanatize_sql failed for string: $sql \n";
}

$parser = new PHPSQLParser\PHPSQLParser();
$parsed = $parser->parse($sql);

echo "-----------------------------------Original Tree---------------------------------------\n";
print_r($parser->parsed);
echo "---------------------------------Original Tree End-------------------------------------\n";
$creator = new PHPSQLParser\PHPSQLCreator();
print($creator->create($parsed));

?>
