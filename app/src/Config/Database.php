<?php

namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                self::loadEnvironment();

                $host = $_ENV['DB_HOST'] ?? 'db';
                $db   = $_ENV['DB_NAME'] ?? 'lotr';
                $user = $_ENV['DB_USER'] ?? 'root';
                $pass = $_ENV['DB_PASS'] ?? '';
                $charset = 'utf8mb4';

                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    private static function loadEnvironment()
    {
        $paths = [
            __DIR__ . '/../../',
            '/var/www/'
        ];

        foreach ($paths as $path) {
            if (file_exists($path . '.env')) {
                $dotenv = Dotenv::createImmutable($path);
                $dotenv->load();
                return;
            }
        }

        throw new InvalidPathException("Unable to find .env file. Searched paths: " . implode(', ', $paths));
    }
}