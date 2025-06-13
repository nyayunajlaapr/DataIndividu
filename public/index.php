<?php
// public/index.php

// --- PENTING: Pengaturan Error Reporting untuk Debugging ---
// Aktifkan ini HANYA SAAT PENGEMBANGAN.
// Di lingkungan produksi, matikan ini atau arahkan error ke log file.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------------------------

/**
 * Logika Router Sederhana
 *
 * File ini berfungsi sebagai entry point utama untuk semua request HTTP.
 * Ia akan mendeteksi apakah request adalah:
 * 1. Request GraphQL (biasanya POST dengan Content-Type: application/json)
 * 2. Request untuk menampilkan halaman HTML (default)
 */

// Periksa apakah request adalah GraphQL (POST request dengan Content-Type JSON)
// Ini adalah cara paling umum untuk mengidentifikasi request GraphQL.
// Jika request menggunakan method GET, atau Content-Type bukan JSON,
// diasumsikan itu adalah request untuk halaman HTML.
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SERVER['CONTENT_TYPE']) &&
    strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {

    // Jika ini adalah request GraphQL, eksekusi logika GraphQL dari graphql.php
    require_once __DIR__ . '/graphql.php';
    exit; // Penting: Hentikan eksekusi setelah graphql.php dijalankan
}

// Jika request bukan POST JSON, anggap ini adalah request untuk halaman HTML (frontend)
// Tampilkan file index.html
// Pastikan public/index.html ada di direktori yang sama.
require_once __DIR__ . '/index.html';
exit;

?>