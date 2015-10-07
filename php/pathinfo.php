<?php


echo '<p>';
echo "PATH_INFO: ";
echo '</p>';

echo '<p>';
print($_SERVER['PATH_INFO']);
echo '</p>';

echo '<p>';
echo "PHP_SELF: ";
echo '</p>';

echo '<p>';
print($_SERVER['PHP_SELF']);
echo '</p>';

echo '<p>';
echo "REQUEST_URI: ";
echo '</p>';

echo '<p>';
print($_SERVER['REQUEST_URI']);
echo '</p>';

echo '<p>';
echo "PATH_TRANSLATED: ";
echo '</p>';

echo '<p>';
print($_SERVER['PATH_TRANSLATED']);
echo '</p>';

echo '<p>';
echo "SCRIPT_NAME: ";
echo '</p>';

echo '<p>';
print($_SERVER['SCRIPT_NAME']);
echo '</p>';

echo '<p>';
echo "SCRIPT_FILENAME: ";
echo '</p>';

echo '<p>';
print($_SERVER['SCRIPT_FILENAME']);
echo '</p>';

?>
