<?php
// config.php untuk Layanan Data Individu

// Detail koneksi diambil dari connection string Anda:
// postgresql://Layanan%20Data%20Individu_owner:npg_cfoEbaJIW4x1@ep-patient-voice-a883xyrq-pooler.eastus2.azure.neon.tech/Layanan%20Data%20Individu?sslmode=require

$host = 'ep-patient-voice-a883xyrq-pooler.eastus2.azure.neon.tech';
$port = '5432';
$dbname = 'Layanan Data Individu'; // Nama database di Neon
$user = 'Layanan Data Individu_owner'; // User dari Neon
$password = 'npg_cfoEbaJIW4x1'; // Password dari Neon
$sslmode = 'require';

// *** PERBAIKAN PENTING DI SINI:
// Mengapit '$dbname' dan '$user' dengan tanda kutip tunggal di dalam string DSN
$dsn = "pgsql:host=$host;port=$port;dbname='$dbname';user='$user';password=$password;sslmode=$sslmode";

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Baris berikut bisa dihapus saat production, hanya untuk debugging awal
    echo "Koneksi ke database Layanan Data Individu berhasil!\n"; // Biarkan dulu untuk konfirmasi
} catch (PDOException $e) {
    die("Koneksi ke database Layanan Data Individu gagal: " . $e->getMessage());
}

?>