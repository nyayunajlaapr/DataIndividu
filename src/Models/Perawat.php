<?php

namespace Iae\LayananDataIndividu\Models;

use PDO;
use PDOException;

class Perawat
{
    private $pdo;
    private $tableName = 'tenaga_kesehatan'; // Perawat menggunakan tabel tenaga_kesehatan
    private $roleValue = 'Perawat'; // Nilai role spesifik untuk Perawat

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllPerawat(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE role = :role ORDER BY id_nakes DESC");
            $stmt->bindParam(':role', $this->roleValue);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all nurses: " . $e->getMessage());
            return [];
        }
    }

    public function getPerawatById(int $id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id_nakes = :id AND role = :role");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':role', $this->roleValue);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching nurse by ID: " . $e->getMessage());
            return false;
        }
    }

    public function createPerawat(array $data)
    {
        // Secara otomatis set role menjadi 'Perawat'
        $data['role'] = $this->roleValue;

        $sql = "INSERT INTO {$this->tableName} (
            nip, nama_lengkap, jenis_kelamin, tanggal_lahir, no_hp,
            no_bpjs, alamat, status_pernikahan, pekerjaan, role
        ) VALUES (
            :nip, :nama_lengkap, :jenis_kelamin, :tanggal_lahir, :no_hp,
            :no_bpjs, :alamat, :status_pernikahan, :pekerjaan, :role
        )";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nip', $data['nip']);
            $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
            $stmt->bindParam(':jenis_kelamin', $data['jenis_kelamin']);
            $stmt->bindParam(':tanggal_lahir', $data['tanggal_lahir']);
            $stmt->bindParam(':no_hp', $data['no_hp']);
            $stmt->bindParam(':no_bpjs', $data['no_bpjs']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':status_pernikahan', $data['status_pernikahan']);
            $stmt->bindParam(':pekerjaan', $data['pekerjaan']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->execute();

            $lastId = $this->pdo->lastInsertId('tenaga_kesehatan_id_nakes_seq'); // Sequence tetap dari tabel tenaga_kesehatan
            return $this->getPerawatById((int)$lastId);
        } catch (PDOException $e) {
            error_log("Error creating nurse: " . $e->getMessage());
            return false;
        }
    }

    public function updatePerawat(int $id, array $data)
    {
        // Pastikan role tidak diubah menjadi non-Perawat dari endpoint ini
        $data['role'] = $this->roleValue;

        $sql = "UPDATE {$this->tableName} SET
            nip = :nip,
            nama_lengkap = :nama_lengkap,
            jenis_kelamin = :jenis_kelamin,
            tanggal_lahir = :tanggal_lahir,
            no_hp = :no_hp,
            no_bpjs = :no_bpjs,
            alamat = :alamat,
            status_pernikahan = :status_pernikahan,
            pekerjaan = :pekerjaan,
            role = :role
            WHERE id_nakes = :id_nakes AND role = :current_role"; // Tambah filter role

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nip', $data['nip']);
            $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
            $stmt->bindParam(':jenis_kelamin', $data['jenis_kelamin']);
            $stmt->bindParam(':tanggal_lahir', $data['tanggal_lahir']);
            $stmt->bindParam(':no_hp', $data['no_hp']);
            $stmt->bindParam(':no_bpjs', $data['no_bpjs']);
            $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':status_pernikahan', $data['status_pernikahan']);
            $stmt->bindParam(':pekerjaan', $data['pekerjaan']);
            $stmt->bindParam(':role', $data['role']);
            $stmt->bindParam(':id_nakes', $id, PDO::PARAM_INT);
            $stmt->bindParam(':current_role', $this->roleValue); // Pastikan hanya mengupdate perawat
            $stmt->execute();

            return $this->getPerawatById($id);
        } catch (PDOException $e) {
            error_log("Error updating nurse: " . $e->getMessage());
            return false;
        }
    }

    public function deletePerawat(int $id): bool
    {
        try {
            // Hapus hanya jika role-nya 'Perawat'
            $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id_nakes = :id AND role = :role");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':role', $this->roleValue);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting nurse: " . $e->getMessage());
            return false;
        }
    }
}