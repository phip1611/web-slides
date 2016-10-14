<?php
error_reporting(null); #production
#error_reporting(E_ALL); #dev
header('Content-Type: application/json');

include __DIR__.'/private/php/pdoConfig.inc.php';
include __DIR__.'/private/php/Json.class.php';
$jsonData = new Json();

try { // Konnte Verbindung zu MySQL-Server herstellen
    $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
} // MySQL-Server nicht erreichbar.
catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't connect to MySQL-Server");
    die($jsonData->getJsonAsString());
}

if (isset($_GET['requestPolling']) && $_GET['requestPolling'] == "true") {
    include __DIR__ . '/private/php/restGetPolling.inc.php';
}
else if (isset($_POST['payload'])) {

    include __DIR__ . '/private/php/restPost.inc.php';
}
else {
    include __DIR__ . '/private/php/restGetRegular.inc.php';
}