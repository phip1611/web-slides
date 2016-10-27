<?php
error_reporting(null); #production
#error_reporting(E_ALL); #dev
header('Content-Type: application/json');
clearstatcache();

include __DIR__.'/private/php/pdoConfig.inc.php';
include __DIR__.'/private/php/Json.class.php';
$jsonData = new Json();

try { // Konnte Verbindung zu MySQL-Server herstellen
    $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
} // MySQL-Server nicht erreichbar.
catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("errorDetail", "couldn't connect to MySQL-Server");
    die($jsonData->getJsonAsString());
}

if (isset($_POST['admin']) && $_POST['admin'] == "true") {
    require __DIR__ . '/private/php/restPostAdmin.inc.php';
}
else if (isset($_GET['presentations']) && $_GET['presentations'] == "true") {
    require __DIR__ . '/private/php/restGetAvailablePresentations.inc.php';
}
else {
    require __DIR__ . '/private/php/restGetWebSlidesData.inc.php';
}