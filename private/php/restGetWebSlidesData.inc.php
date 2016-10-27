<?php
# DATEN DER REST-ABFRAGE HOLEN
try {
    $sql = file_get_contents(__DIR__.'/../src/sql/getData.sql');
    $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
    $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

    $stmt = $pdo->query($sql);
    // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist relevant
    $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);
    $jsonData->setData("referenceDatetime", $row[0]);
    $jsonData->setData("command", $row[1]);
    $jsonData->setData("options", json_decode($row[2]));
    $jsonData->setData("presentationIdentifier", $row[3]);
    $jsonData->setData("message", "success");
    die($jsonData->getJsonAsString());
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("errorDetail", "couldn't get data from table");
    die($jsonData->getJsonAsString());
}