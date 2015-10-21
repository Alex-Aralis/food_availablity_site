<?php
DEFINE("ENC_BYTES",32);
DEFINE("IV_BYTES", 16);
DEFINE("CYPHER_AND_MODE", "AES-256-CBC"); 

function pkcs7_pad($data, $size){
    $length = $size - strlen($data)%$size;
    return $data . str_repeat(chr($length), $length);
}

function pkcs7_unpad($data){
    return substr($data, 0, -ord($data[strlen($data) - 1]));
}
?>
