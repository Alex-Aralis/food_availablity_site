<?php
$error_file = "/srv/http/food-availability-site/logs/php.log";

print_r($_POST);

$conn = new PDO("mysql:host=localhost;dbname=food_account_data", "root", "skunkskunk2");

//setting up the exeption handling
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//escape and quote user naem and password sent from user
$safe_name = $conn->quote($_POST['userName']);
$safe_pw = $conn->quote($_POST['password']);
$safe_email = $conn->quote($_POST['email']);
$safe_ip = $conn->quote($_SERVER['REMOTE_ADDR']);

//check the number of accounts created in the last 2 hours by ip
$stmt = $conn->query(
"SELECT COUNT(*) AS recent_account_creations from account_creation_ips ".
    "WHERE ip=$safe_ip AND timestamp>=DATE_SUB(NOW(), INTERVAL 2 HOUR) ".
    "GROUP BY ip"
);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($result);

//dont allow more than 10 accounts in 2 hours
if ($result[0]['recent_account_creations'] >= 10){
   die("Too many accounts created from ip, try again later");
}

$result = null;
$stmt = null;

//create new user in the database with no privlages
$rows_effected = $conn->exec("CREATE USER $safe_name@'localhost' IDENTIFIED BY $safe_pw");

//allow new user to have food_user role
$rows_effected = $conn->exec("GRANT food_user TO $safe_name@'localhost'");

//make food_user the default role for the new user
$rows_effected = $conn->exec("SET DEFAULT ROLE food_user FOR $safe_name@'localhost'");

//limit some account resources for the new user (cant be done in role)
$rows_effected = $conn->exec(
"GRANT USAGE ON *.* TO $safe_name@'localhost' WITH ".
    "MAX_USER_CONNECTIONS 5 ".
    "MAX_CONNECTIONS_PER_HOUR 700 ".
    "MAX_QUERIES_PER_HOUR 700 ".
    "MAX_STATEMENT_TIME 1 "
);

$rows_effected = $conn->exec("INSERT INTO accounts SET user_name=$safe_name, email=$safe_email");

$rows_effected = $conn->exec("INSERT INTO account_creation_ips SET ip=$safe_ip");

$conn = null;
?>
