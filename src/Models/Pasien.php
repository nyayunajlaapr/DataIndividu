<?php
// src/Models/Pasien.php

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
     * Mengambil satu data pasien berdasarkan ID.
     * @param int $id ID Pasien
     * @return array|false
     */
    public function getPasienById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id_pasien, nik, nama_lengkap, jenis_kelamin, tanggal_lahir, no_bpjs, pekerjaan, status_pernikahan FROM pasien WHERE id_pasien = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching Pasien by ID: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil data pasien.");
        }
    }

    /**
     * Mengambil semua data pasien.
     * @return array
     */
    public function getAllPasien()
    {
        try {
            $stmt = $this->pdo->query("SELECT id_pasien, nik, nama_lengkap, jenis_kelamin, tanggal_lahir, no_bpjs, pekerjaan, status_pernikahan FROM pasien ORDER BY id_pasien ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all Pasien: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil semua data pasien.");
        }
    }

    /**
     * Membuat data pasien baru.
     * @param array $data Data pasien yang akan disimpan.
     * @return array|null Data pasien yang baru dibuat, atau null jika gagal.
     */
    public function createPasien(array $data)
    {
        try {
            $sql = "INSERT INTO pasien (nik, nama_lengkap, jenis_kelamin, tanggal_lahir, no_bpjs, pekerjaan, status_pernikahan)
                    VALUES (:nik, :nama_lengkap, :jenis_kelamin, :tanggal_lahir, :no_bpjs, :pekerjaan, :status_pernikahan)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':nik', $data['nik'] ?? null);
            $stmt->bindValue(':nama_lengkap', $data['namaLengkap'] ?? null); // GraphQL uses camelCase, DB uses snake_case
            $stmt->bindValue(':jenis_kelamin', $data['jenisKelamin'] ?? null);
            $stmt->bindValue(':tanggal_lahir', $data['tanggalLahir'] ?? null);
            $stmt->bindValue(':no_bpjs', $data['noBpjs'] ?? null);
            $stmt->bindValue(':pekerjaan', $data['pekerjaan'] ?? null);
            $stmt->bindValue(':status_pernikahan', $data['statusPernikahan'] ?? null);

            $executeResult = $stmt->execute();

            if ($executeResult) {
                $lastId = $this->pdo->lastInsertId();
                if ($lastId) {
                    return $this->getPasienById((int)$lastId);
                }
            }
            return null;

        } catch (PDOException $e) {
            error_log("Error creating Pasien: " . $e->getMessage());
            var_dump("PDOException caught in createPasien: " . $e->getMessage());
            throw new \Exception("Database Error Pasien: Gagal membuat pasien baru: " . $e->getMessage());
        } catch (\Exception $e) {
             error_log("General Error creating Pasien: " . $e->getMessage());
             var_dump("General Exception caught in createPasien: " . $e->getMessage());
             throw new \Exception("General Error Pasien: Gagal membuat pasien baru: " . $e->getMessage());
        }
    }

    /**
     * Memperbarui data pasien yang sudah ada.
     * @param int $id ID Pasien
     * @param array $data Data pasien yang akan diperbarui.
     * @return array|null Data pasien yang diperbarui, atau null jika gagal.
     */
    public function updatePasien($id, array $data)
    {
        try {
            $setClauses = [];
            $bindValues = [];

            // Mapping GraphQL camelCase ke kolom database snake_case
            $fieldMap = [
                'nik' => 'nik',
                'namaLengkap' => 'nama_lengkap',
                'jenisKelamin' => 'jenis_kelamin',
                'tanggalLahir' => 'tanggal_lahir',
                'noBpjs' => 'no_bpjs',
                'pekerjaan' => 'pekerjaan',
                'statusPernikahan' => 'status_pernikahan',
            ];

            foreach ($data as $key => $value) {
                if (isset($fieldMap[$key])) {
                    $dbColumn = $fieldMap[$key];
                    $setClauses[] = "$dbColumn = :$key";
                    $bindValues[":$key"] = $value; // Use the GraphQL key as parameter name
                }
            }

            if (empty($setClauses)) {
                return $this->getPasienById($id); // Tidak ada yang diupdate
            }

            $sql = "UPDATE pasien SET " . implode(', ', $setClauses) . " WHERE id_pasien = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($bindValues as $param => $value) {
                // IMPORTANT: Bind value using the GraphQL key, not the DB column name,
                // because that's what's used in the :$key placeholder above.
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            return $this->getPasienById($id);

        } catch (PDOException $e) {
            error_log("Error updating Pasien: " . $e->getMessage());
            var_dump("PDOException caught in updatePasien: " . $e->getMessage());
            throw new \Exception("Database Error Pasien: Gagal memperbarui data pasien: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error updating Pasien: " . $e->getMessage());
            var_dump("General Exception caught in updatePasien: " . $e->getMessage());
            throw new \Exception("General Error Pasien: Gagal memperbarui data pasien: " . $e->getMessage());
        }
    }

    /**
     * Menghapus data pasien berdasarkan ID.
     * @param int $id ID Pasien
     * @return bool True jika berhasil dihapus, false jika tidak.
     */
    public function deletePasien($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pasien WHERE id_pasien = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting Pasien: " . $e->getMessage());
            var_dump("PDOException caught in deletePasien: " . $e->getMessage());
            throw new \Exception("Database Error Pasien: Gagal menghapus pasien: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error deleting Pasien: " . $e->getMessage());
            var_dump("General Exception caught in deletePasien: " . $e->getMessage());
            throw new \Exception("General Error Pasien: Gagal menghapus pasien: " . $e->getMessage());
        }
    }
}