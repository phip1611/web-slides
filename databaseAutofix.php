<?php
error_reporting(null); #production
#error_reporting(E_ALL); #dev
header('Content-Type: application/json');


require __DIR__ . '/private/php/pdoConfig.inc.php';
require __DIR__ . '/private/php/Json.class.php';
$jsonData = new Json();

try { // Konnte Verbindung zu MySQL-Server herstellen
    $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
} // MySQL-Server nicht erreichbar.
catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't connect to MySQL-Server");
    die($jsonData->getJsonAsString());
}

if (isset($_GET['recreate']) && $_GET['recreate'] == "true") {
    $sql = file_get_contents(__DIR__.'/private/src/sql/dropDbTable.sql');
    $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
    $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);
    try {
        $stmt = $pdo->query($sql);
    } catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't drop table from MySQL-Server");
        die($jsonData->getJsonAsString());
    }
}

# TESTEN OB TABELLE VORHANDEN IST
try { #Tabelle vorhanden
    $sql = file_get_contents(__DIR__.'/private/src/sql/getData.sql');
    $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
    $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);
    $stmt = $pdo->query($sql);
    // Tabelle offensichtlich vorhanden, nur ohne Daten
    if ($stmt->rowCount() == 0) {
        $sql = file_get_contents(__DIR__.'/private/src/sql/insertDefaultData.sql');
        $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
        $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);
        $pdo->query($sql);
    }
} catch (PDOException $ex) {#Tabelle nicht vorhanden
    $sql1 = file_get_contents(__DIR__.'/private/src/sql/createDbTable.sql');
    $sql1 = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql1);
    $sql1 = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql1);
    $sql2 = file_get_contents(__DIR__.'/private/src/sql/insertDefaultData.sql');
    $sql2 = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql2);
    $sql2 = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql2);
    # VERSUCHEN TABELLE ZU ERSTELLEN MIT DEFAULT DATEN
    try { #Tabelle konnte erstellt werden
        $pdo->query($sql1);
        $pdo->query($sql2);
    } catch (PDOException $ex) { #Tabelle konnte nicht erstellt werden
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't create MySQL-table");
        die($jsonData->getJsonAsString());
    }
}

$jsonData->setData("message", "success");
die($jsonData->getJsonAsString());