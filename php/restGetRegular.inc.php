<?php
# TEST OB TABELLE VORHANDEN
try { // Tabelle ist vorhanden
    $pdo->query(
        'SELECT `presentation_id`, `command`, `last_updated`'
        .'FROM `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'`'
        .'WHERE `id` = 1 LIMIT 0,1'
    );
} // Tabelle ist nicht vorhanden
catch (PDOException $ex) {
    $sqlStmts = file_get_contents(__DIR__.'/php/createTable.sql');
    $sqlStmts = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sqlStmts);
    $sqlStmts = preg_split ('/;/', $sqlStmts);
    array_pop($sqlStmts); // das letzte Element entfernen, da es sonst ne leere Query gibt..
    try { // Tabelle erstellen/anlegen
        foreach ($sqlStmts as $sqlStmt) {
            $pdo->query($sqlStmt);
        }
    } // Fehler, abbrechen
    catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "table not found and couldn't be created");
        die($jsonData->getJsonAsString());
    }
}

# FALLS TABELLE DA, ABER LEER, DATEN-ZEILE ANLEGEN
try {
    $stmt = $pdo->query(
        'SELECT `presentation_id`, `command`, `last_updated`'
        .'FROM `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'`'
        .'WHERE `id` = 1 LIMIT 0,1'
    );
    // Leere Tabelle, das ist nicht okay, Datensatz einfügen
    // einziger Fehlerfall: Tabelle besteht aber irgendwer oder irgendwas hat sie geleert
    if ($stmt->rowCount() == 0) {
        $pdo->query('INSERT INTO `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'`() VALUES ()');
    }
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "empty table and data couln't be inserted");
    die($jsonData->getJsonAsString());
}

# DATEN DER REST-ABFRAGE HOLEN
try {
    $stmt = $pdo->query(
        'SELECT `presentation_id`, `command`, `last_updated`'
        .'FROM `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'`'
        .'WHERE `id` = 1 LIMIT 0,1'
    );
    // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist relevant
    while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
        $jsonData->setData("presentationId", $row[0]);
        $jsonData->setData("command", $row[1]);
        $jsonData->setData("lastUpdated", $row[2]);
    }
    $jsonData->setData("message", "success");
    $stmt = null;
    die($jsonData->getJsonAsString());
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't get data from table");
    die($jsonData->getJsonAsString());
}