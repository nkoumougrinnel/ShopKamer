<?php

/**
 * config/db.php
 *
 * Connexion MySQLi centralisée pour ShopKamer.
 * Utilisation : require_once __DIR__ . '/db.php';
 * puis appeler getDbConnection().
 */

declare(strict_types=1);

function getDbConnection(): mysqli
{
    static $mysqli = null;

    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $host = '127.0.0.1';
    $user = 'root';
    $password = '';
    $database = 'shopkamer';
    $port = 3306;

    $mysqli = new mysqli($host, $user, $password, $database, $port);
    $mysqli->set_charset('utf8mb4');

    return $mysqli;
}
