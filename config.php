<?php
// config.php

// File ini HANYA bertanggung jawab untuk mengembalikan array konfigurasi.
// Tidak ada koneksi database, echo, atau die() di sini.

return [
    'db' => [
        // Detail koneksi diambil dari connection string Anda:
        // postgresql://data_individu_owner:npg_O6A2zLwdvqKh@ep-patient-voice-a883xyrq-pooler.eastus2.azure.neon.tech/data_individu?sslmode=require
        'host' => 'ep-patient-voice-a883xyrq-pooler.eastus2.azure.neon.tech',
        'port' => '5432',
        'dbname' => 'data_individu', // Nama database baru TANPA spasi
        'user' => 'data_individu_owner', // User baru TANPA spasi
        'password' => 'npg_O6A2zLwdvqKh',
        'sslmode' => 'require', // Sangat penting untuk koneksi Neon
    ],
    // Anda bisa menambahkan konfigurasi aplikasi lain di sini di masa mendatang, contoh:
    // 'app' => [
    //     'env' => 'development', // 'development', 'production', 'testing'
    //     'debug' => true,      // true untuk development, false untuk production
    //     'timezone' => 'Asia/Jakarta',
    // ],
];