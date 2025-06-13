<?php

namespace Config;

use PDO;
use PDOException;

/**
 * Cette class Database est un Pattern singleton qui créer une seule instance de PDO.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=localhost;port=3306;dbname=material_manager", "root", "secret");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de donnée: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->pdo;
    }

    public static function checkConnection(): bool
    {
        return self::getInstance() === null;
    }
}
