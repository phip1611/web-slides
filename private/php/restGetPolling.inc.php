<?php
// zu diesem Zeitpunkt ist bereits klar, dass die MySQL-Verbindung steht
// dank rest.php, die kümmert sich da schon drum!
set_time_limit(0);
$restReferenceDatetime = -1;
$restCommand = '';
$restOptions = '{}';

$i = 0;

$sql = file_get_contents(__DIR__.'/../src/sql/getData.sql');
$sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
$sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

while (true) {
    try {
        $stmt = $pdo->query($sql);
        // es gibt sowieso nur einen Datensatz bzw. nur die erste Zeile ist relevant
        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            if ($restReferenceDatetime == -1) { // aller erster Durchlauf
                $restReferenceDatetime = $row[0];
            }
            if ($restReferenceDatetime != $row[0]) {
                $jsonData->setData("referenceDatetime", $row[0]);
                $jsonData->setData("command", $row[1]);
                $jsonData->setData("options", json_decode($row[2])->options);
                $jsonData->setData("message", "success");
                die($jsonData->getJsonAsString());
            }
        }
    } catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't get data from table");
        die($jsonData->getJsonAsString());
    }
    usleep(100000); // 100ms
}