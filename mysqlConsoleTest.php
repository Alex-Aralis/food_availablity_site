<head>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/components/MysqlConsole.css.html'; ?>
</head>


<div style='display:flex;' height=500>
<div id='console1'></div>
<div id='console2'></div>
</div>

<?php
include $_SERVER['DOCUMENT_ROOT'].'/components/MysqlConsole.js.php';
?>

<script>
var wampURL = 'ws://localhost:8081/ws';
var wampRealm = 'realm1';

$(function(){
    var console1 = new MysqlConsole('console1', wampURL, wampRealm);
    var console2 = new MysqlConsole('console2', wampURL, wampRealm);

    console1.open();
    console2.open();
});
</script>
