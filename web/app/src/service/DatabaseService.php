<?php

namespace App\Acme\service;

use PDO;
use PDOException;

class DatabaseService
{
    /**
     * @var PDO
     */
    private static $pdo;

    /**
     * @return PDO
     */
    public static function getPdo(): PDO
    {
        if (empty(self::$pdo)) {
            try {
                $dsn = 'mysql:host=mysql;dbname=test;charset=utf8;port=3306';
                self::$pdo = new PDO($dsn, 'dev', 'dev');
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }

        return self::$pdo;
    }
}
