<?php
require_once "food_openssl.php";

$root_conn = new PDO("mysql:host=localhost;dbname=food_account_data", "root", "skunkskunk2");

//setting up the exeption handling
$root_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$root_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

if(!is_numeric($session_id = $_COOKIE['session_id'])){
    die("session id not numeric");
}


$stmt = $root_conn->query("SELECT user_name, pw_enc_key, iv FROM account_sessions WHERE id=$session_id");
$root_conn = null;

$result = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = null;

$user_name = $result['user_name'];
$db_pw_key = $result['pw_enc_key'];

$iv = $result['iv'];

$pw = pkcs7_unpad(openssl_decrypt(
    base64_decode($_COOKIE['enc_pw']),
    CYPHER_AND_MODE,
    $db_pw_key,
    0,
    $iv
));

$user_conn = new PDO("mysql:host=localhost;dbname=food_account_data", $user_name, $pw);

//setting up the exeption handling
$user_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$user_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$stmt = $user_conn->query("SELECT user_name, email FROM user_accounts_view");

$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(array('username' => $result['user_name'], 'email' => $result['email']));
?>
