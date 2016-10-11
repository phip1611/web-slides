<?php
$sPattern = '/({command=([A-z0-9-:])+})/';
if (!preg_match($sPattern, $_POST['payload'])) {
    $jsonData->setData('message', 'error');
    $jsonData->setData('error-detail', 'post-request has to match with [(payload=)]'.$sPattern.', yours was '.$_POST['payload']);
    die($jsonData->getJsonAsString());
}
$command = $_POST['payload'];
$command = str_replace('{', '', $command);
$command = str_replace('}', '', $command);
$command = str_replace('command=', '', $command);


# TEST OB TABELLE VORHANDEN
try { // Tabelle ist vorhanden
    $pdo->query(
        'SELECT `presentation_id`, `command`, `last_updated`'
        .'FROM `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'`'
        .'WHERE `id` = 1 LIMIT 0,1'
    );
} // Tabelle ist nicht vorhanden
catch (PDOException $ex) {
    $sqlStmts = file_get_contents(__DIR__.'/createTable.sql');
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

// Gesendetes Kommando (POST) in DB schreiben
try {
    #############################################################
    // Sicherstellen, dass das Kommando überschrieben wird, da sonst wenn aus "next" wieder "next" wird
    // on update current timestamp von mysql nicht funktioniert!
    echo $command;
    $stmt = $pdo->query(
        'UPDATE `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'` SET `command` = \'skip\''
        .'WHERE `id` = 1'
    );
    $stmt = $pdo->query(
        'UPDATE `'.$mysqlCredentials->tablePrefix.$mysqlCredentials->tableName.'` SET `command` = \''.$command.'\''
        .'WHERE `id` = 1'
    );
    $jsonData->setData("message", "success");
    $stmt = null;
    die($jsonData->getJsonAsString());
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't update data in table");
    die($jsonData->getJsonAsString());
}