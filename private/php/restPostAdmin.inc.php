<?php
# DATEN DER REST-ABFRAGE HOLEN
if (!isset($_POST['payload']) || empty($_POST['payload'])) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "no payload data attached to post request");
    die($jsonData->getJsonAsString());
}
if (!isset($_POST['whatToUpdate'])) {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "not specified what to update");
    die($jsonData->getJsonAsString());
}


if (!isset($_POST['whatToUpdate']) || $_POST['whatToUpdate'] == 'all') {
    try {
        $sql = file_get_contents(__DIR__ . '/../src/sql/updateAllData.sql');
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
        $stmt->bindParam(':options',$options);
        $stmt->execute();
        $jsonData->setData("message", "success");
        die($jsonData->getJsonAsString());
    } catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't update data in table");
        die($jsonData->getJsonAsString());
    }
}
else if (isset($_POST['whatToUpdate']) && $_POST['whatToUpdate'] == 'presentationIdentifier') {
    try {
        $sql = file_get_contents(__DIR__ . '/../src/sql/updatePresentationIdentifierData.sql');
        $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
        $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':presentationIdentifier',$_POST['payload']);
        $stmt->execute();
        $jsonData->setData("message", "success");
        die($jsonData->getJsonAsString());
    } catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't update data in table");
        die($jsonData->getJsonAsString());
    }
}
else if (isset($_POST['whatToUpdate']) && $_POST['whatToUpdate'] == 'command') {
        try {
            $sql = file_get_contents(__DIR__ . '/../src/sql/updateCommandData.sql');
            $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
            $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':command',$_POST['payload']);
            $stmt->execute();
            $jsonData->setData("message", "success");
            die($jsonData->getJsonAsString());
        } catch (PDOException $ex) {
            $jsonData->setData("message", "error");
            $jsonData->setData("error_detail", "couldn't update data in table");
            die($jsonData->getJsonAsString());
        }
}
else if (isset($_POST['whatToUpdate']) && $_POST['whatToUpdate'] == 'options') {
    try {
        $sql = file_get_contents(__DIR__ . '/../src/sql/updateOptionsData.sql');
        $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
        $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);

        $data = json_decode($_POST['payload'], true);
        if ($data == null) {
            $jsonData->setData("message", "error");
            $jsonData->setData("error_detail", "payload is no valid json");
            die($jsonData->getJsonAsString());
        }
        unset($data); // nur überprüfen ob valides JSON

        $stmt = $pdo->prepare($sql);

        // Das ist notwengig um die x-url-form-encoded-Bedingten
        // Formatierungen, wie \" statt " wegzukriegen
        $options = json_encode(json_decode($_POST['payload']));
        $options = '{"options":'.$options.'}';
        $stmt->bindParam(':options',$options);
        $stmt->execute();
        $jsonData->setData("message", "success");
        die($jsonData->getJsonAsString());
    } catch (PDOException $ex) {
        $jsonData->setData("message", "error");
        $jsonData->setData("error_detail", "couldn't update data in table");
        die($jsonData->getJsonAsString());
    }
}
else {
    $jsonData->setData("message", "error");
    $jsonData->setData("error_detail", "unknown whatToUpdate parameter");
    die($jsonData->getJsonAsString());
}
