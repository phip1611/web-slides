<?php
error_reporting(null);
header('Content-Type: application/json');

$mysqlData = include('mysql_data.config.php');
$dataMessage = $dataLastUpdated = $dataCurrentSlideIdentifier = $dataPresentationId = '';

$pdo = null;
$dsn = 'mysql:host='.$mysqlData->host.';dbname='.$mysqlData->database.';charset='.$mysqlData->charset;
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // connection succeed
    $pdo = new PDO($dsn, $mysqlData->username, $mysqlData->password, $opt);
    try { // Tabelle gefunden

        // sicher stellen, dass Tabelle in jedem Fall mit der einen Zeile vorhanden ist
        $stmt = $pdo->query(
              'SELECT `presentation_id`, `current_slide_identifier`, `last_updated`'
             .'FROM `'.$mysqlData->tableName.'`'
             .'WHERE `id` = 1 LIMIT 0,1'
        );
        if ($stmt->rowCount() == 0) { // Leere Tabelle, das ist nicht okay, Datensatz einfügen
            $pdo->query(
                 'INSERT INTO `'.$mysqlData->tableName.'`'
                .'(`presentation_id`) VALUES (1)'
            );
        }
        $stmt = $pdo->query(
            'SELECT `presentation_id`, `current_slide_identifier`, `last_updated`'
            .'FROM `'.$mysqlData->tableName.'`'
            .'WHERE `id` = 1 LIMIT 0,1'
        );
        // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist releavant
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $dataCurrentSlideIdentifier = $row[1];
            $dataLastUpdated = $row[2];
            $dataPresentationId = $row[0];
        }
        $stmt = null;
        $dataMessage = 'success';
    } catch (PDOException $ex) { // Tabelle nicht gefunden, versuchen zu erstellen
        try {
            $pdo->query(
                'CREATE TABLE `'.$mysqlData->tableName.'` ('
                .'  `id` int(10) UNSIGNED NOT NULL,'
                .' `presentation_id` int(10) UNSIGNED NOT NULL,'
                .' `current_slide_identifier` char(255) NOT NULL DEFAULT \'\','
                .' `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP'
                .') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
            );
            $pdo->query(
                 'ALTER TABLE `'.$mysqlData->tableName.'`'
                .'ADD PRIMARY KEY (`id`);'
            );
            $pdo->query(
                'ALTER TABLE `'.$mysqlData->tableName.'`'
                .'MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;'
            );
            $pdo->query(
                'ALTER TABLE `web-slides_sessions` CHANGE `last_updated` `last_updated` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;'
            );


            $pdo->query(
                'INSERT INTO `'.$mysqlData->tableName.'`'
               .'(`presentation_id`) VALUES (1)'
            );
            $stmt = $pdo->query(
                'SELECT `presentation_id`, `current_slide_identifier`, `last_updated`'
                .'FROM `'.$mysqlData->tableName.'`'
                .'WHERE `id` = 1 LIMIT 0,1'
            );
            // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist releavant
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $dataCurrentSlideIdentifier = $row[1];
                $dataLastUpdated = $row[2];
                $dataPresentationId = $row[0];
            }
            $stmt = null;
            $dataMessage = 'success';
        } catch (PDOException $ex) { // Abbrechen, konnte Tabelle nicht erstellen
            $dataMessage = 'error (table not found and couldn\'t be created)';
        }
    }
} catch (PDOException $ex) { // MySQL-Server nicht erreichbar
    $dataMessage = 'error (couln\'t connect to mysql)';
}

if ($dataMessage != 'success') {
    echo json_encode(array(
        'message' => $dataMessage
    ));
} else {
    echo json_encode(array(
        'message' => $dataMessage,
        'lastUpdated' => $dataLastUpdated,
        'currentPageIdentifier' => $dataCurrentSlideIdentifier,
        'presentationId' => $dataPresentationId
    ));
}
