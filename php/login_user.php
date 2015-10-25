<?php
require_once "/srv/http/food-availability-site/php/food_openssl.php";
$error_file = "/srv/http/food-availability-site/logs/php.log";

$root_conn = new PDO("mysql:host=localhost;dbname=food_account_data", "root", "skunkskunk2");

//setting up the exeption handling
$root_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//returns fetches as correct type if the mysql type exists in php
$root_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$safe_user_name = $root_conn->quote($_POST['userName']);
$root_conn->exec("DELETE FROM food_account_data.account_login_failures WHERE " .
                  "user_name=$safe_user_name AND ts<DATE_SUB(NOW(), INTERVAL 1 HOUR)");

$stmt = $root_conn->query("SELECT COUNT(*) as recent_failures FROM food_account_data.account_login_failures " .
                    "WHERE user_name=$safe_user_name");

$result = $stmt->fetchColumn();

if($result > 5){
    die("TOO MANY FAILED LOGIN ATTEMPTS FOR USER!!!");
}

try{
    $conn = new PDO("mysql:host=localhost;dbname=food_account_data", $_POST['userName'], $_POST['password']);
}catch(PDOException $e){
    $root_conn->exec("INSERT INTO food_account_data.account_login_failures SET user_name=$safe_user_name");
    die("FAILED LOGIN");
}
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

//$stmt = $root_conn->prepare("INSERT INTO account_sessions SET user_name=:userName, pw_enc_key=:enc_key, iv=:iv");
$stmt = $root_conn->prepare("CALL add_session(:userName, :enc_key, :iv, 5, 1)");
$stmt->bindParam(":userName", $_POST['userName']);
$stmt->bindParam(":enc_key", $enc_key);
$stmt->bindParam(":iv", $iv);

$stmt->execute();

$safe_name = $conn->quote($_POST['userName']);

$stmt = $root_conn->query("SELECT id, ts FROM account_sessions WHERE id=".
                          "(SELECT MAX(id) FROM account_sessions WHERE user_name=$safe_name)");

$result = $stmt->fetch(PDO::FETCH_ASSOC);

$session_id = $result['id'];

$enc_pw = openssl_encrypt(
    pkcs7_pad($_POST['password'], 16),
    CYPHER_AND_MODE,
    $enc_key,
    0,
    $iv
);

//sets a cookie for 1 day
setrawcookie("enc_pw", base64_encode($enc_pw), time() + 86400, "/");
setrawcookie("session_id", $session_id, time() + 86400, "/");
?>
