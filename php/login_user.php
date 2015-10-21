<?php
require_once "/srv/http/food-availability-site/php/food_openssl.php";
$error_file = "/srv/http/food-availability-site/logs/php.log";


$conn = new PDO("mysql:host=localhost;dbname=food_account_data", $_POST['userName'], $_POST['password']);

//setting up the exeption handling
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

//create encryption key
$enc_key = openssl_random_pseudo_bytes(\ENC_BYTES, $strong);
if(!$strong){
    echo "openssl random encryption key generation not cryptographically strong!!!";
}

//create initialization vector
$iv = openssl_random_pseudo_bytes(IV_BYTES, $strong);
if(!$strong){
    echo "openssl random initialization vector generation not cryptographically strong!!!";
}

$root_conn = new PDO("mysql:host=localhost;dbname=food_account_data", "root", "skunkskunk2");

//setting up the exeption handling
$root_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$root_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$stmt = $root_conn->prepare("INSERT INTO account_sessions SET user_name=:userName, pw_enc_key=:enc_key, iv=:iv");
$stmt->bindParam(":userName", $_POST['userName']);
$stmt->bindParam(":enc_key", $enc_key);
$stmt->bindParam(":iv", $iv);

$stmt->execute();

$safe_name = $conn->quote($_POST['userName']);

$stmt = $root_conn->query("SELECT id, ts FROM account_sessions WHERE id=".
                          "(SELECT MAX(id) FROM account_sessions WHERE user_name=$safe_name)");

$result = $stmt->fetch(PDO::FETCH_ASSOC);

//print_r($result);

$session_id = $result['id'];

//echo "\npassword: " . $_POST['password'];
//echo "\npassword: " . bin2hex($_POST['password']);
//echo "\npadded password: " . bin2hex(pkcs7_pad($_POST['password'], 16));
//echo "\nunpadded password: " . pkcs7_unpad(pkcs7_pad($_POST['password'], 16));
//echo "\nunpadded password: " . bin2hex(pkcs7_unpad(pkcs7_pad($_POST['password'], 16)));


//echo "\nenc_key: ";
//var_dump(bin2hex($enc_key));
//echo "\niv: ";
//var_dump(bin2hex($iv));


$enc_pw = openssl_encrypt(
    pkcs7_pad($_POST['password'], 16),
    CYPHER_AND_MODE,
    $enc_key,
    0,
    $iv
);

//echo "\nenc_pw: ";
//var_dump(bin2hex($enc_pw));

//sets a cookie for 1 day
setrawcookie("enc_pw", base64_encode($enc_pw), time() + 86400, "/");
setrawcookie("session_id", $session_id, time() + 86400, "/");

$stmt = $root_conn->query("SELECT pw_enc_key FROM account_sessions where id=$session_id");

$result = $stmt->fetch(PDO::FETCH_ASSOC);

$db_pw_key = $result['pw_enc_key'];

$pw = pkcs7_unpad(openssl_decrypt(
    $enc_pw,
    CYPHER_AND_MODE,
    $db_pw_key,
    0,
    $iv
));

//echo "\npw: $pw";
?>
