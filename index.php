<?php
error_reporting(null);
?>
<html>
<head>
    <title>Web-Slides: Präsentation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="src/css/reset.css">
    <link rel="stylesheet" href="src/css/style.css">
    <link rel="stylesheet" href="src/css/responsive.css">
    <script src="src/js/logic.js"></script>
</head>

<body>
<div id="container" class="box-shadow">
    <noscript><p>Bitte aktiviere JavaScript in deinem Browser.</p></noscript>
    <?php
    $slideIdentifier = '';
    require __DIR__ . '/private/php/pdoConfig.inc.php';
    try {
        $pdo = new PDO($dsn, $mysqlCredentials->username, $mysqlCredentials->password, $opt);
        $sql = file_get_contents('private/src/sql/getPresentationToDisplayIdentifier.sql');
        $sql = str_replace('%TABLE_PREFIX%', $mysqlCredentials->tablePrefix, $sql);
        $sql = str_replace('%TABLE_NAME%', $mysqlCredentials->tableName, $sql);
        try {
            $stmt = $pdo->query($sql);
            if (!$stmt->rowCount() == 0) {
                while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                    $slideIdentifier = $row[0];
                }
            }
            if (empty($slideIdentifier)) {
                // kann passieren wenn in der DB noch nichts steht :)
                echo file_get_contents(__DIR__ . '/private/src/html/error.html');
            } else {
                if (file_exists(__DIR__ . '/private/src/html/presentation'.$slideIdentifier.'.html')) {
                    echo file_get_contents(__DIR__ . '/private/src/html/presentation'.$slideIdentifier.'.html');
                } else {
                    echo file_get_contents(__DIR__ . '/private/src/html/error.html');
                }
            }
        } catch (PDOException $ex) {
            echo file_get_contents(__DIR__ . '/private/src/html/error.html');
        }
    } catch (PDOException $ex) {
        echo file_get_contents(__DIR__ . '/private/src/html/error.html');
    }
    ?>
</div>
</body>
</html>