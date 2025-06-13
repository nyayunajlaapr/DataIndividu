<?php
// public/graphql.php

// Pastikan autoload Composer sudah dijalankan
require_once __DIR__ . '/../vendor/autoload.php';

// Impor kelas-kelas GraphQL dan model-model Anda
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Iae\LayananDataIndividu\Database;
use Iae\LayananDataIndividu\Models\TenagaKesehatan;
use Iae\LayananDataIndividu\Models\Pasien;
use Iae\LayananDataIndividu\Models\Dokter;
use Iae\LayananDataIndividu\Models\Perawat;

// --- Bagian 1: Memuat Environment Variables dan Koneksi Database ---
use Dotenv\Dotenv;

// Muat variabel lingkungan dari .env file
// Ini penting untuk mendapatkan kredensial database
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Buat instance koneksi database
$database = new Database();
$pdo = $database->connect(); // Metode connect() sudah diperbarui untuk SSL di Database.php

// Buat instance dari setiap model Anda
$tenagaKesehatanModel = new TenagaKesehatan($pdo);
$pasienModel = new Pasien($pdo);
$dokterModel = new Dokter($pdo);
$perawatModel = new Perawat($pdo);

// --- Bagian 2: Definisi Tipe GraphQL ---

// Definisi TenagaKesehatanType
$tenagaKesehatanType = new ObjectType([
    'name' => 'TenagaKesehatan',
    'description' => 'Representasi data Tenaga Kesehatan',
    'fields' => [
        'id_nakes' => Type::int(),
        'nip' => Type::string(),
        'role' => Type::string(),
        'str' => Type::string(),
    ],
]);

// Definisi PasienType
$pasienType = new ObjectType([
    'name' => 'Pasien',
    'description' => 'Representasi data Pasien',
    'fields' => [
        'id_pasien' => Type::int(),
        'nik' => Type::string(),
        'nama_lengkap' => Type::string(),
        'jenis_kelamin' => Type::string(),
        'tanggal_lahir' => Type::string(), // Tanggal bisa diwakili sebagai String atau custom Scalar DateType
        'no_bpjs' => Type::string(),
        'pekerjaan' => Type::string(),
        'status_pernikahan' => Type::string(),
    ],
]);

// Definisi DokterType
$dokterType = new ObjectType([
    'name' => 'Dokter',
    'description' => 'Representasi data Dokter',
    'fields' => [
        'id_dokter' => Type::int(),
        'id_nakes' => Type::int(), // Foreign key ke TenagaKesehatan
        'nip' => Type::string(),
        'nama_dokter' => Type::string(),
        'status' => Type::string(),
        'str' => Type::string(),
        'spesialisasi' => Type::string(),
        'no_hp' => Type::string(),
    ],
]);

// Definisi PerawatType
$perawatType = new ObjectType([
    'name' => 'Perawat',
    'description' => 'Representasi data Perawat',
    'fields' => [
        'id_perawat' => Type::int(),
        'id_nakes' => Type::int(), // Foreign key ke TenagaKesehatan
        'nip' => Type::string(),
        'nama_perawat' => Type::string(),
        'status' => Type::string(),
        'str' => Type::string(),
        'spesialisasi' => Type::string(),
        'no_hp' => Type::string(),
    ],
]);

// --- Bagian 3: Definisi QueryType (Untuk Operasi Read) ---

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        // TenagaKesehatan Queries
        'tenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => [
                'id_nakes' => Type::nonNull(Type::int()), // ID wajib
            ],
            'resolve' => function ($rootValue, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->getTenagaKesehatanById($args['id_nakes']);
            },
        ],
        'allTenagaKesehatan' => [
            'type' => Type::listOf($tenagaKesehatanType), // Mengembalikan daftar TenagaKesehatan
            'resolve' => function () use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->getAllTenagaKesehatan();
            },
        ],

        // Pasien Queries
        'pasien' => [
            'type' => $pasienType,
            'args' => [
                'id_pasien' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($pasienModel) {
                return $pasienModel->getPasienById($args['id_pasien']);
            },
        ],
        'allPasien' => [
            'type' => Type::listOf($pasienType),
            'resolve' => function () use ($pasienModel) {
                return $pasienModel->getAllPasien();
            },
        ],

        // Dokter Queries
        'dokter' => [
            'type' => $dokterType,
            'args' => [
                'id_dokter' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($dokterModel) {
                return $dokterModel->getDokterById($args['id_dokter']);
            },
        ],
        'allDokter' => [
            'type' => Type::listOf($dokterType),
            'resolve' => function () use ($dokterModel) {
                return $dokterModel->getAllDokter();
            },
        ],

        // Perawat Queries
        'perawat' => [
            'type' => $perawatType,
            'args' => [
                'id_perawat' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($perawatModel) {
                return $perawatModel->getPerawatById($args['id_perawat']);
            },
        ],
        'allPerawat' => [
            'type' => Type::listOf($perawatType),
            'resolve' => function () use ($perawatModel) {
                return $perawatModel->getAllPerawat();
            },
        ],
    ],
]);

// --- Bagian 4: Definisi MutationType (Untuk Operasi Create, Update, Delete) ---

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        // TenagaKesehatan Mutations
        'createTenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => [
                'nip' => Type::nonNull(Type::string()),
                'role' => Type::nonNull(Type::string()),
                'str' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->createTenagaKesehatan($args);
            },
        ],
        'updateTenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => [
                'id_nakes' => Type::nonNull(Type::int()),
                'nip' => Type::string(),
                'role' => Type::string(),
                'str' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($tenagaKesehatanModel) {
                $id = $args['id_nakes'];
                unset($args['id_nakes']); // Hapus ID dari data yang akan diupdate
                return $tenagaKesehatanModel->updateTenagaKesehatan($id, $args);
            },
        ],
        'deleteTenagaKesehatan' => [
            'type' => Type::boolean(),
            'args' => [
                'id_nakes' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->deleteTenagaKesehatan($args['id_nakes']);
            },
        ],

        // Pasien Mutations
        'createPasien' => [
            'type' => $pasienType,
            'args' => [
                'nik' => Type::nonNull(Type::string()),
                'nama_lengkap' => Type::nonNull(Type::string()),
                'jenis_kelamin' => Type::string(),
                'tanggal_lahir' => Type::string(),
                'no_bpjs' => Type::string(),
                'pekerjaan' => Type::string(),
                'status_pernikahan' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($pasienModel) {
                return $pasienModel->createPasien($args);
            },
        ],
        'updatePasien' => [
            'type' => $pasienType,
            'args' => [
                'id_pasien' => Type::nonNull(Type::int()),
                'nik' => Type::string(),
                'nama_lengkap' => Type::string(),
                'jenis_kelamin' => Type::string(),
                'tanggal_lahir' => Type::string(),
                'no_bpjs' => Type::string(),
                'pekerjaan' => Type::string(),
                'status_pernikahan' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($pasienModel) {
                $id = $args['id_pasien'];
                unset($args['id_pasien']);
                return $pasienModel->updatePasien($id, $args);
            },
        ],
        'deletePasien' => [
            'type' => Type::boolean(),
            'args' => [
                'id_pasien' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($pasienModel) {
                return $pasienModel->deletePasien($args['id_pasien']);
            },
        ],

        // Dokter Mutations
        'createDokter' => [
            'type' => $dokterType,
            'args' => [
                'idNakes' => Type::int(), // idNakes (camelCase) di GraphQL, akan dipetakan ke id_nakes di PHP model
                'nip' => Type::string(),
                'namaDokter' => Type::nonNull(Type::string()), // namaDokter (camelCase)
                'status' => Type::string(),
                'str' => Type::string(),
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(), // noHp (camelCase)
            ],
            'resolve' => function ($rootValue, $args) use ($dokterModel) {
                return $dokterModel->createDokter($args);
            },
        ],
        'updateDokter' => [
            'type' => $dokterType,
            'args' => [
                'id_dokter' => Type::nonNull(Type::int()),
                'idNakes' => Type::int(),
                'nip' => Type::string(),
                'namaDokter' => Type::string(),
                'status' => Type::string(),
                'str' => Type::string(),
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($dokterModel) {
                $id = $args['id_dokter'];
                unset($args['id_dokter']);
                return $dokterModel->updateDokter($id, $args);
            },
        ],
        'deleteDokter' => [
            'type' => Type::boolean(),
            'args' => [
                'id_dokter' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($dokterModel) {
                return $dokterModel->deleteDokter($args['id_dokter']);
            },
        ],

        
        'createPerawat' => [
            'type' => $perawatType,
            'args' => [
                'idNakes' => Type::int(), 
                'nip' => Type::string(),
                'namaPerawat' => Type::nonNull(Type::string()), 
                'status' => Type::string(),
                'str' => Type::string(),
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(), 
            ],
            'resolve' => function ($rootValue, $args) use ($perawatModel) {
                return $perawatModel->createPerawat($args);
            },
        ],
        'updatePerawat' => [
            'type' => $perawatType,
            'args' => [
                'id_perawat' => Type::nonNull(Type::int()),
                'idNakes' => Type::int(),
                'nip' => Type::string(),
                'namaPerawat' => Type::string(),
                'status' => Type::string(),
                'str' => Type::string(),
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(),
            ],
            'resolve' => function ($rootValue, $args) use ($perawatModel) {
                $id = $args['id_perawat'];
                unset($args['id_perawat']);
                return $perawatModel->updatePerawat($id, $args);
            },
        ],
        'deletePerawat' => [
            'type' => Type::boolean(),
            'args' => [
                'id_perawat' => Type::nonNull(Type::int()),
            ],
            'resolve' => function ($rootValue, $args) use ($perawatModel) {
                return $perawatModel->deletePerawat($args['id_perawat']);
            },
        ],
    ],
]);

// --- Bagian 5: Membangun Skema GraphQL dan Eksekusi Query ---

// Buat skema GraphQL dengan Query dan Mutation yang sudah didefinisikan
$schema = new Schema([
    'query' => $queryType,
    'mutation' => $mutationType,
]);

// Ambil raw input dari body request (untuk POST request GraphQL)
$rawInput = file_get_contents('php://input');
$rawInput = json_decode($rawInput, true);

// Periksa apakah input JSON valid
if (json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json; charset=UTF-8', true, 400);
    echo json_encode(['errors' => [['message' => 'Invalid JSON input']]]);
    exit;
}

// rootValue bisa diisi jika ada data atau objek yang perlu diakses oleh semua resolver
$rootValue = [];

try {
    // Eksekusi query GraphQL
    $variables = isset($rawInput['variables']) ? $rawInput['variables'] : null;
    $result = GraphQL::executeQuery($schema, $rawInput['query'], null, $rootValue, $variables);

    // Set header Content-Type ke application/json
    header('Content-Type: application/json; charset=UTF-8');
    // Encode hasil eksekusi ke format JSON dan kirim sebagai response
    echo json_encode($result->toArray());
} catch (\Exception $e) {
    // Tangani error yang mungkin terjadi selama eksekusi GraphQL
    header('Content-Type: application/json; charset=UTF-8', true, 500);
    error_log("GraphQL Error: " . $e->getMessage());
    echo json_encode([
        'errors' => [
            ['message' => 'Internal server error: ' . $e->getMessage()]
        ]
    ]);
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);