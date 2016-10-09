<?php
error_reporting(null);
header('Content-Type: application/json');

$dataMessage = '';

if (isset($_POST['payload'])) {
    $dataAction = $_POST['payload'];
    $dataAction = str_replace('{', '', $dataAction);
    $dataAction = str_replace('}', '', $dataAction);
    $dataAction = str_replace('action=', '', $dataAction);

    if (empty($dataAction) || preg_match('/([0-9A-z-_])+/', $dataAction)) {
        $mysqlData = include('mysql_data.config.php');
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
                    'UPDATE `'.$mysqlData->tableName.'` SET `current_slide_identifier` = \''.$dataAction.'\''
                    .'WHERE `id` = 1'
                );
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
                        'INSERT INTO `'.$mysqlData->tableName.'`'
                        .'(`presentation_id`, `current_slide_identifier`) VALUES (1,\''.$dataAction.'\')'
                    );

                    $dataMessage = 'success';
                } catch (PDOException $ex) { // Abbrechen, konnte Tabelle nicht erstellen
                    $dataMessage = 'error (table not found and couldn\'t be created)';
                }
            }
        } catch (PDOException $ex) { // MySQL-Server nicht erreichbar
            $dataMessage = 'error (couln\'t connect to mysql)';
        }
    } else {
        $dataMessage = 'error (invalid POST data)';
    }
} else {
    $dataMessage = 'error (no POST data)';
}


echo json_encode(
    array(
        'message' => $dataMessage
    ));
