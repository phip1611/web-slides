<?php
# DATEN DER REST-ABFRAGE HOLEN
try {
    $sql = file_get_contents(__DIR__.'/../src/sql/getData.sql');
    $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
    $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

    $stmt = $pdo->query($sql);
    // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist relevant
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $jsonData->setData("referenceDatetime", $row[0]);
        $jsonData->setData("command", $row[1]);
        $jsonData->setData("options", json_decode($row[2])->options);
        // decode, da das als JSON in der DB liegt :)
        // options-key ist bereits im DB-JSON,
        // den wollen wir nicht 2mal :)
    }
    $jsonData->setData("message", "success");
    $stmt = null;
    die($jsonData->getJsonAsString());
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't get data from table");
    die($jsonData->getJsonAsString());
}