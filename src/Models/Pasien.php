<?php

namespace Iae\LayananDataIndividu\Models;

use PDO;
use PDOException;

class Pasien
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil semua data pasien.
     * @return array
     */
    public function getAllPasien(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM pasien ORDER BY id_pasien DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all patients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mengambil data pasien berdasarkan ID.
     * @param int $id
     * @return array|false
     */
    public function getPasienById(int $id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM pasien WHERE id_pasien = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching patient by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Membuat data pasien baru.
     * @param array $data Data pasien (namaLengkap, jenisKelamin, dll.)
     * @return array|false Data pasien yang baru dibuat atau false jika gagal
     */
    public function createPasien(array $data)
    {
        // Hapus 'alamat' dari daftar kolom
        $sql = "INSERT INTO pasien (
            nama_lengkap, jenis_kelamin, tanggal_lahir, no_bpjs,
            status_pernikahan, pekerjaan, nik
        ) VALUES (
            :nama_lengkap, :jenis_kelamin, :tanggal_lahir, :no_bpjs,
            :status_pernikahan, :pekerjaan, :nik
        )";

        try {
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':nama_lengkap', $data['namaLengkap']);
            $stmt->bindParam(':jenis_kelamin', $data['jenisKelamin']);
            $stmt->bindParam(':tanggal_lahir', $data['tanggalLahir']);
            $stmt->bindParam(':no_bpjs', $data['noBpjs']);
            // Hapus baris ini: $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':status_pernikahan', $data['statusPernikahan']);
            $stmt->bindParam(':pekerjaan', $data['pekerjaan']);
            $stmt->bindParam(':nik', $data['nik']);

            $stmt->execute();

            $lastId = $this->pdo->lastInsertId('pasien_id_pasien_seq');
            return $this->getPasienById((int)$lastId);
        } catch (PDOException $e) {
            error_log("Error creating patient: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui data pasien.
     * @param int $id
     * @param array $data Data yang akan diperbarui
     * @return array|false Data pasien yang diperbarui atau false jika gagal
     */
    public function updatePasien(int $id, array $data)
    {
        // Hapus 'alamat' dari klausa SET
        $sql = "UPDATE pasien SET
            nama_lengkap = :nama_lengkap,
            jenis_kelamin = :jenis_kelamin,
            tanggal_lahir = :tanggal_lahir,
            no_bpjs = :no_bpjs,
            status_pernikahan = :status_pernikahan,
            pekerjaan = :pekerjaan,
            nik = :nik
            WHERE id_pasien = :id_pasien";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nama_lengkap', $data['namaLengkap']);
            $stmt->bindParam(':jenis_kelamin', $data['jenisKelamin']);
            $stmt->bindParam(':tanggal_lahir', $data['tanggalLahir']);
            $stmt->bindParam(':no_bpjs', $data['noBpjs']);
            // Hapus baris ini: $stmt->bindParam(':alamat', $data['alamat']);
            $stmt->bindParam(':status_pernikahan', $data['statusPernikahan']);
            $stmt->bindParam(':pekerjaan', $data['pekerjaan']);
            $stmt->bindParam(':nik', $data['nik']);
            $stmt->bindParam(':id_pasien', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $this->getPasienById($id);
        } catch (PDOException $e) {
            error_log("Error updating patient: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus data pasien.
     * @param int $id
     * @return bool True jika berhasil dihapus, false jika gagal
     */
    public function deletePasien(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pasien WHERE id_pasien = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting patient: " . $e->getMessage());
            return false;
        }
    }
}