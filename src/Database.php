<?php
// src/Database.php

namespace Iae\LayananDataIndividu;

use PDO;
use PDOException;

class Database
{
    private $host;
    private $port;
    private $dbName;
    private $user;
    private $password;
    private $sslMode;

    public function __construct()
    {
        // Pastikan variabel lingkungan dimuat.
        // $_ENV hanya tersedia jika register_globals = On atau jika Dotenv library
        // menggunakannya. getenv() lebih disarankan jika register_globals Off.
        // Jika Anda menggunakan library Dotenv (seperti vlucas/phpdotenv),
        // pastikan Anda telah memanggil $dotenv->load() di awal aplikasi (misalnya di index.php).

        $this->host = getenv('DB_HOST') ?? 'localhost';
        $this->port = getenv('DB_PORT') ?? '5432';
        $this->dbName = getenv('DB_NAME') ?? 'default_db'; // Menggunakan DB_NAME sesuai .env
        $this->user = getenv('DB_USER') ?? 'root';     // Menggunakan DB_USER sesuai .env
        $this->password = getenv('DB_PASS') ?? '';
        $this->sslMode = getenv('DB_SSLMODE') ?? null;
    }

    public function connect()
    {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbName}";

        // Tambahkan sslmode jika ada dan tidak null
        if ($this->sslMode) {
            $dsn .= ";sslmode={$this->sslMode}";
        }

        // Debug: tampilkan DSN, user, dan dbName yang dipakai ke error log PHP
        error_log("Attempting DB connection with DSN: $dsn");
        error_log("DB User: {$this->user}");
        error_log("DB Name: {$this->dbName}");


        try {
            $pdo = new PDO($dsn, $this->user, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Anda bisa menambahkan pesan log jika koneksi berhasil (opsional)
            error_log("Koneksi database berhasil!");
            return $pdo;
        } catch (PDOException $e) {
            error_log("Koneksi database gagal: " . $e->getMessage());
            throw $e; // Lempar error ke atas, agar ditangani di graphql.php
        }
    }
}

?>