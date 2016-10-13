<?php
error_reporting(E_ALL);
header('Content-Type: application/json');


include __DIR__ . '/php/Json.class.php';
$jsonData = new Json();

$mysqlCredentials = include(__DIR__ . '/php/mysqlCredentials.config.php');
$pdo = null;
$dsn = 'mysql:host='.$mysqlCredentials->host.';dbname='.$mysqlCredentials->database.';charset='.$mysqlCredentials->charset;
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try { // Konnte Verbindung zu MySQL-Server herstellen
    $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
} // MySQL-Server nicht erreichbar.
catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't connect to MySQL-Server");
    die($jsonData->getJsonAsString());
}

if (isset($_GET['requestPolling']) && $_GET['requestPolling']) {
    include __DIR__ . '/php/restGetPolling.inc.php';
}
else if (isset($_POST['payload'])) {

    include __DIR__ . '/php/restPost.inc.php';
}
else {
    include __DIR__ . '/php/restGetRegular.inc.php';
}