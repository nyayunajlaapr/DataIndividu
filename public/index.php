<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php'; // Ini akan menginisialisasi $pdo

use GraphQL\Type\Schema;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Iae\LayananDataIndividu\Models\Pasien;
use Iae\LayananDataIndividu\Models\Dokter;
use Iae\LayananDataIndividu\Models\TenagaKesehatan;
use Iae\LayananDataIndividu\Models\Perawat;

// Inisialisasi Model
$pasienModel = new Pasien($pdo);
$dokterModel = new Dokter($pdo);
$tenagaKesehatanModel = new TenagaKesehatan($pdo);
$perawatModel = new Perawat($pdo);
// $rekamMedisModel = new RekamMedis($pdo); // DIHAPUS

// --- Definisikan GraphQL Types ---

// $rekamMedisType DIHAPUS

// Tipe untuk Pasien (referensi ke rekamMedis dihapus)
$pasienType = new ObjectType([
    'name' => 'Pasien',
    'fields' => [
        'id_pasien' => Type::id(),
        'nama_lengkap' => Type::string(),
        'jenis_kelamin' => Type::string(),
        'tanggal_lahir' => Type::string(),
        'no_bpjs' => Type::string(),
        'status_pernikahan' => Type::string(),
        'pekerjaan' => Type::string(),
        'nik' => Type::string(),
        // 'rekamMedis' => [...] DIHAPUS
    ]
]);

// Tipe untuk Dokter (alamat dihapus dari definisi GraphQL jika tidak ada di DB Dokter)
$dokterType = new ObjectType([
    'name' => 'Dokter',
    'description' => 'Representasi data dokter.',
    'fields' => [
        'id_dokter' => Type::nonNull(Type::id()),
        'id_nakes' => Type::int(),        // Kolom baru sesuai tabel
        'nip' => Type::string(),          // Kolom baru sesuai tabel
        'nama_dokter' => Type::string(),  // Nama kolom sudah benar
        'status' => Type::string(),       // Kolom baru sesuai tabel
        'str' => Type::string(),          // Kolom baru sesuai tabel
        'spesialisasi' => Type::string(),
        'no_hp' => Type::string(),
    ],
]);

// Tipe untuk TenagaKesehatan (alamat dihapus dari definisi GraphQL jika tidak ada di DB TenagaKesehatan)
$tenagaKesehatanType = new ObjectType([
    'name' => 'TenagaKesehatan',
    'description' => 'Representasi data tenaga kesehatan (misalnya perawat, apoteker).',
    'fields' => [
        'id_nakes' => Type::nonNull(Type::id()),
        'nip' => Type::string(),
        'nama_lengkap' => Type::string(),
        'jenis_kelamin' => Type::string(),
        'tanggal_lahir' => Type::string(),
        'no_hp' => Type::string(),
        'no_bpjs' => Type::string(),
        // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel tenaga_kesehatan
        'status_pernikahan' => Type::string(),
        'pekerjaan' => Type::string(),
        'role' => Type::string(), // Contoh: "Perawat", "Apoteker", "Admin"
    ],
]);

// Tipe untuk Perawat (alamat dihapus dari definisi GraphQL jika tidak ada di DB Perawat)
$perawatType = new ObjectType([
    'name' => 'Perawat',
    'description' => 'Representasi data perawat (subset dari TenagaKesehatan).',
    'fields' => [
        'id_nakes' => Type::nonNull(Type::id()), // Menggunakan id_nakes sebagai ID Perawat
        'nip' => Type::string(),
        'nama_lengkap' => Type::string(),
        'jenis_kelamin' => Type::string(),
        'tanggal_lahir' => Type::string(),
        'no_hp' => Type::string(),
        'no_bpjs' => Type::string(),
        // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel perawat
        'status_pernikahan' => Type::string(),
        'pekerjaan' => Type::string(),
        // 'role' tidak perlu di sini karena sudah implicit 'Perawat'
    ],
]);


// --- Definisikan Query ---

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        // Query untuk Pasien
        'pasien' => [
            'type' => $pasienType,
            'args' => ['id_pasien' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($pasienModel) {
                return $pasienModel->getPasienById((int)$args['id_pasien']);
            }
        ],
        'allPasien' => [
            'type' => Type::listOf($pasienType),
            'resolve' => function ($root, $args) use ($pasienModel) {
                return $pasienModel->getAllPasien();
            }
        ],
        // Query untuk Dokter
      'dokter' => [
            'type' => $dokterType,
            'args' => [
                'id_dokter' => Type::nonNull(Type::id()),
            ],
            'resolve' => function ($root, $args) use ($dokterModel) {
                return $dokterModel->getDokterById((int)$args['id_dokter']);
            }
        ],
        'allDokter' => [
            'type' => Type::listOf($dokterType),
            'resolve' => function ($root, $args) use ($dokterModel) {
                return $dokterModel->getAllDokter();
            }
        ],
        // Query untuk TenagaKesehatan
        'tenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => ['id_nakes' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->getTenagaKesehatanById((int)$args['id_nakes']);
            }
        ],
        'allTenagaKesehatan' => [
            'type' => Type::listOf($tenagaKesehatanType),
            'resolve' => function ($root, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->getAllTenagaKesehatan();
            }
        ],
        // Query untuk Perawat
        'perawat' => [
            'type' => $perawatType,
            'args' => ['id_nakes' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($perawatModel) {
                return $perawatModel->getPerawatById((int)$args['id_nakes']);
            }
        ],
        'allPerawat' => [
            'type' => Type::listOf($perawatType),
            'resolve' => function ($root, $args) use ($perawatModel) {
                return $perawatModel->getAllPerawat();
            }
        ],
        // Query untuk Rekam Medis (DIHAPUS)
        // 'rekamMedis' => [ ... ],
        // 'allRekamMedis' => [ ... ],
    ],
]);

// --- Definisikan Mutation ---

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        // Pasien Mutations
        'createPasien' => [
            'type' => $pasienType,
            'args' => [
                'namaLengkap' => Type::nonNull(Type::string()),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noBpjs' => Type::string(),
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
                'nik' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($pasienModel) {
                return $pasienModel->createPasien($args);
            }
        ],
        'updatePasien' => [
            'type' => $pasienType,
            'args' => [
                'id_pasien' => Type::nonNull(Type::int()),
                'namaLengkap' => Type::string(),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noBpjs' => Type::string(),
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
                'nik' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($pasienModel) {
                $id = $args['id_pasien'];
                unset($args['id_pasien']);
                return $pasienModel->updatePasien($id, $args);
            }
        ],
        'deletePasien' => [
            'type' => Type::boolean(),
            'args' => ['id_pasien' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($pasienModel) {
                return $pasienModel->deletePasien((int)$args['id_pasien']);
            }
        ],
        // Mutation untuk Dokter (alamat dihapus jika tidak ada di DB Dokter)
           'createDokter' => [
            'type' => $dokterType,
            'args' => [
                'idNakes' => Type::int(),             // GraphQL arg untuk id_nakes
                'nip' => Type::string(),              // GraphQL arg untuk nip
                'namaDokter' => Type::nonNull(Type::string()), // GraphQL arg untuk nama_dokter
                'status' => Type::string(),           // GraphQL arg untuk status
                'str' => Type::string(),              // GraphQL arg untuk str
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($dokterModel) {
                return $dokterModel->createDokter($args);
            }
        ],
        'updateDokter' => [
            'type' => $dokterType,
            'args' => [
                'id_dokter' => Type::nonNull(Type::id()),
                'idNakes' => Type::int(),
                'nip' => Type::string(),
                'namaDokter' => Type::string(),
                'status' => Type::string(),
                'str' => Type::string(),
                'spesialisasi' => Type::string(),
                'noHp' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($dokterModel) {
                $id = (int)$args['id_dokter'];
                unset($args['id_dokter']);
                return $dokterModel->updateDokter($id, $args);
            }
        ],
        'deleteDokter' => [ // Pastikan ini juga ada
            'type' => Type::boolean(),
            'args' => [
                'id_dokter' => Type::nonNull(Type::id()),
            ],
            'resolve' => function ($root, $args) use ($dokterModel) {
                return $dokterModel->deleteDokter((int)$args['id_dokter']);
            }
        ],
        // Mutation untuk TenagaKesehatan (alamat dihapus jika tidak ada di DB TenagaKesehatan)
        'createTenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => [
                'nip' => Type::string(),
                'namaLengkap' => Type::nonNull(Type::string()),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noHp' => Type::string(),
                'noBpjs' => Type::string(),
                // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel tenaga_kesehatan
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
                'role' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->createTenagaKesehatan($args);
            }
        ],
        'updateTenagaKesehatan' => [
            'type' => $tenagaKesehatanType,
            'args' => [
                'id_nakes' => Type::nonNull(Type::id()),
                'nip' => Type::string(),
                'namaLengkap' => Type::string(),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noHp' => Type::string(),
                'noBpjs' => Type::string(),
                // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel tenaga_kesehatan
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
                'role' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($tenagaKesehatanModel) {
                $id = (int)$args['id_nakes'];
                unset($args['id_nakes']);
                return $tenagaKesehatanModel->updateTenagaKesehatan($id, $args);
            }
        ],
        'deleteTenagaKesehatan' => [
            'type' => Type::boolean(),
            'args' => ['id_nakes' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($tenagaKesehatanModel) {
                return $tenagaKesehatanModel->deleteTenagaKesehatan((int)$args['id_nakes']);
            }
        ],
        // Mutation untuk Perawat (alamat dihapus jika tidak ada di DB Perawat)
        'createPerawat' => [
            'type' => $perawatType,
            'args' => [
                'nip' => Type::string(),
                'namaLengkap' => Type::nonNull(Type::string()),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noHp' => Type::string(),
                'noBpjs' => Type::string(),
                // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel perawat
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($perawatModel) {
                return $perawatModel->createPerawat($args);
            }
        ],
        'updatePerawat' => [
            'type' => $perawatType,
            'args' => [
                'id_nakes' => Type::nonNull(Type::id()),
                'nip' => Type::string(),
                'namaLengkap' => Type::string(),
                'jenisKelamin' => Type::string(),
                'tanggalLahir' => Type::string(),
                'noHp' => Type::string(),
                'noBpjs' => Type::string(),
                // 'alamat' => Type::string(), // Hapus ini jika kolom 'alamat' tidak ada di tabel perawat
                'statusPernikahan' => Type::string(),
                'pekerjaan' => Type::string(),
            ],
            'resolve' => function ($root, $args) use ($perawatModel) {
                $id = (int)$args['id_nakes'];
                unset($args['id_nakes']);
                return $perawatModel->updatePerawat($id, $args);
            }
        ],
        'deletePerawat' => [
            'type' => Type::boolean(),
            'args' => ['id_nakes' => Type::nonNull(Type::id())],
            'resolve' => function ($root, $args) use ($perawatModel) {
                return $perawatModel->deletePerawat((int)$args['id_nakes']);
            }
        ],
        // Mutation untuk RekamMedis (DIHAPUS)
        // 'createRekamMedis' => [ ... ],
        // 'updateRekamMedis' => [ ... ],
        // 'deleteRekamMedis' => [ ... ],
    ],
]);

// --- Bangun Skema GraphQL ---

$schema = new Schema([
    'query' => $queryType,
    'mutation' => $mutationType,
]);

// --- Tangani Request GraphQL ---

try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (!isset($input['query'])) {
        throw new \Exception('GraphQL query is missing.');
    }

    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    $operationName = isset($input['operationName']) ? $input['operationName'] : null;

    $rootValue = [];

    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues, $operationName);
    $output = $result->toArray();

} catch (\Exception $e) {
    $output = [
        'errors' => [
            [
                'message' => $e->getMessage(),
                'locations' => [],
            ]
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($output);

?>