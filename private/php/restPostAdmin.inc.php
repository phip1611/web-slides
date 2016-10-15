<?php
# DATEN DER REST-ABFRAGE HOLEN
if (!isset($_POST['payload']) || empty($_POST['payload'])) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "no payload data attached to post request");
    die($jsonData->getJsonAsString());
}

try {
    $sql = file_get_contents(__DIR__.'/../src/sql/updateData.sql');
    $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
    $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

    $data = json_decode($_POST['payload'], true);

    if ($data == null) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "payload is no valid json");
        die($jsonData->getJsonAsString());
    }

    if (!isset($data['command']) || !isset($data['presentationIdentifier']) || !isset($data['options'])) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "no valid post data: must be a json with the fields command, presentationIdentifier, options");
        die($jsonData->getJsonAsString());
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':command',$data['command']);
    $stmt->bindParam(':presentationIdentifier',$data['presentationIdentifier']);
    $options = '{"options":'.json_encode($data['options']).'}';
    @$stmt->bindParam(':options',$options);
    // Unterdrücken der Notice:  Only variables should be passed by reference
    $stmt->execute();
    $jsonData->setData("message", "success");
    die($jsonData->getJsonAsString());
} catch (PDOException $ex) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "couldn't update data in table");
    die($jsonData->getJsonAsString());
}