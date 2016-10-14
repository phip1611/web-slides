<?php
$mysqlCredentials = include(__DIR__ . '/mysqlCredentials.config.php');
$pdo = null;
$dsn = 'mysql:host='.$mysqlCredentials->host.';dbname='.$mysqlCredentials->database.';charset='.$mysqlCredentials->charset;
$opt = [
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
];