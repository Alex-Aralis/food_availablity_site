<?php
#namespace PHPSQLParser;

require_once '/srv/http/food-availability-site/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLParser.php';
require_once '/srv/http/food-availability-site/php/PHP-SQL-Parser/src/PHPSQLParser/PHPSQLCreator.php';

$error_file = "/srv/http/food-availability-site/logs/php.log";

class safe_db {
    public $conn;    
    private $parser;
    private $creator;
    private $prunings;
    private $dbname;
    private $token_tester;
    private $sanatized_parsed_sql;

    function __Construct($dbtype, $dbhost, $dbname, $dbuser, $dbpassword, 
            $token_whitelist = array(
            "SELECT", "expr_type", "alias", "base_expr", 
            "sub_tree", "delim", "FROM", "table", 
            "no_quotes", "parts", "hints", "ref_type",
            "WHERE", "join_type", "ref_clause")){
        global $conn, $parser, $creator;

        //create PDO connecion object
        $conn = new PDO("$dbtype:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
        $GLOBALS['dbname'] = $dbname;

        //setting up the exeption handling
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        //returns fetches as correct type if the mysql type exists in php
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
       
        //set the token tester
        $this->replace_whitelist($token_whitelist);
        
        //create global parser and creator
        $parser = new PHPSQLParser\PHPSQLParser();
        $creator = new PHPSQLParser\PHPSQLCreator();
    }

    public function replace_whitelist($token_whitelist){
        global $token_tester;

        $new_token_tester = [];

        foreach($token_whitelist as $token){
            $new_token_tester[$token] = true;
        }
          
        $token_tester = $new_token_tester;
    }
   
    public function append_whitelist($token_whitelist){
        global $token_tester;

        foreach($token_whitelist as $token){
            $token_tester[$token] = true;
        }
    }
 
    private function parse_sql($sql){
        global $parser;

        $parser->parse($sql);

        return $parser->parsed;
    }

    private function create_sql($parsed_sql){
        global $creator;

        $creator->create($parsed_sql);
 
        return $creator->created;
    }

    private function sanatize_parsed_sql($parsed_sql, &$sanatized_parsed_sql){
        global $token_tester;

        $new_prunings = 0;

        foreach($parsed_sql as $token => $value){
            if (is_int($token) || $token_tester[$token]){
                if(gettype($value) === "array"){
                    $sanatized_parsed_sql[$token] = [];
                    $new_prunings += $this->sanatize_parsed_sql($parsed_sql[$token], $sanatized_parsed_sql[$token]);
                }else{
                    $sanatized_parsed_sql[$token] = $value;
                }
            }else{
                $new_prunings += 1;
            }
        }

         return $new_prunings;
     }
    
    public function sanatize_sql($raw_sql){
        global $prunings, $creator, $sanatized_parsed_sql;

        $raw_parsed_sql = $this->parse_sql($raw_sql);
        
        $sanatized_parsed_sql = [];
        $prunings = $this->sanatize_parsed_sql($raw_parsed_sql, $sanatized_parsed_sql);

        $creator->create($sanatized_parsed_sql);
   
        return $prunings;
    }

    public function get_conn(){
       global $conn;
 
       return $conn;
    }

    public function get_prunings(){
        global $prunings;

        return $prunings;
    }

    public function get_sanatized_sql(){
        global $creator;

        return $creator->created;
    }

    public function get_safe_parsed_sql(){
        global $sanatized_parsed_sql;
        
        return $sanatized_parsed_sql;
    }

    public function execute(){
        global $conn;
        
        return $conn->query($this->get_sanatized_sql());
    }

}
?>
