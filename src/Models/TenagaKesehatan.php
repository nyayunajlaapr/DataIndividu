<?php
// src/Models/TenagaKesehatan.php

namespace Iae\LayananDataIndividu\Models;

use PDO;
use PDOException;

class TenagaKesehatan
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil satu data tenaga kesehatan berdasarkan ID.
     * @param int $id ID Tenaga Kesehatan
     * @return array|false
     */
    public function getTenagaKesehatanById($id)
    {
        try {
            // HANYA SELECT KOLOM YANG ADA DI TABEL ANDA
            $stmt = $this->pdo->prepare("SELECT id_nakes, nip, role, str FROM tenaga_kesehatan WHERE id_nakes = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching TenagaKesehatan by ID: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil data tenaga kesehatan.");
        }
    }

    /**
     * Mengambil semua data tenaga kesehatan.
     * @return array
     */
    public function getAllTenagaKesehatan()
    {
        try {
            // HANYA SELECT KOLOM YANG ADA DI TABEL ANDA
            $stmt = $this->pdo->query("SELECT id_nakes, nip, role, str FROM tenaga_kesehatan ORDER BY id_nakes ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all TenagaKesehatan: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil semua data tenaga kesehatan.");
        }
    }

    /**
     * Membuat data tenaga kesehatan baru.
     * @param array $data Data tenaga kesehatan yang akan disimpan.
     * @return array|null Data tenaga kesehatan yang baru dibuat, atau null jika gagal.
     */
    public function createTenagaKesehatan(array $data)
    {
        try {
            // HANYA INSERT KE KOLOM YANG ADA DI TABEL ANDA
            $sql = "INSERT INTO tenaga_kesehatan (nip, role, str)
                    VALUES (:nip, :role, :str)";
            $stmt = $this->pdo->prepare($sql);

            // HANYA BIND NILAI UNTUK KOLOM YANG ADA
            $stmt->bindValue(':nip', $data['nip'] ?? null);
            $stmt->bindValue(':role', $data['role'] ?? null);
            $stmt->bindValue(':str', $data['str'] ?? null);

            $executeResult = $stmt->execute();

            if ($executeResult) {
                // Untuk PostgreSQL IDENTITY, lastInsertId() biasanya bekerja tanpa argumen
                $lastId = $this->pdo->lastInsertId();

                if ($lastId) {
                    return $this->getTenagaKesehatanById((int)$lastId);
                }
            }
            return null;

        } catch (PDOException $e) {
            error_log("Error creating TenagaKesehatan: " . $e->getMessage());
            // Tambahkan ini agar error juga terlihat jelas di terminal saat debugging
           
            throw new \Exception("Database Error TenagaKesehatan: Gagal membuat tenaga kesehatan baru: " . $e->getMessage());
        } catch (\Exception $e) {
             error_log("General Error creating TenagaKesehatan: " . $e->getMessage());
            
             throw new \Exception("General Error TenagaKesehatan: Gagal membuat tenaga kesehatan baru: " . $e->getMessage());
        }
    }

    /**
     * Memperbarui data tenaga kesehatan yang sudah ada.
     * @param int $id ID Tenaga Kesehatan
     * @param array $data Data tenaga kesehatan yang akan diperbarui.
     * @return array|null Data tenaga kesehatan yang diperbarui, atau null jika gagal.
     */
    public function updateTenagaKesehatan($id, array $data)
    {
        try {
            $setClauses = [];
            $bindValues = [];

            // HANYA MAPPING KOLOM YANG ADA DI TABEL ANDA
            $fieldMap = [
                'nip' => 'nip',
                'role' => 'role',
                'str' => 'str',
            ];

            foreach ($data as $key => $value) {
                if (isset($fieldMap[$key])) {
                    $dbColumn = $fieldMap[$key];
                    $setClauses[] = "$dbColumn = :$key";
                    $bindValues[":$key"] = $value;
                }
            }

            if (empty($setClauses)) {
                return $this->getTenagaKesehatanById($id); // Tidak ada yang diupdate
            }

            $sql = "UPDATE tenaga_kesehatan SET " . implode(', ', $setClauses) . " WHERE id_nakes = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($bindValues as $param => $value) {
                $stmt->bindValue($param, $value);
            }

            $stmt->execute();
            // var_dump($stmt->errorInfo()); // Debugging

            return $this->getTenagaKesehatanById($id);

        } catch (PDOException $e) {
            error_log("Error updating TenagaKesehatan: " . $e->getMessage());
            
            throw new \Exception("Database Error TenagaKesehatan: Gagal memperbarui data tenaga kesehatan: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error updating TenagaKesehatan: " . $e->getMessage());
            
            throw new \Exception("General Error TenagaKesehatan: Gagal memperbarui data tenaga kesehatan: " . $e->getMessage());
        }
    }

    /**
     * Menghapus data tenaga kesehatan berdasarkan ID.
     * @param int $id ID Tenaga Kesehatan
     * @return bool True jika berhasil dihapus, false jika tidak.
     */
    public function deleteTenagaKesehatan($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tenaga_kesehatan WHERE id_nakes = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            // var_dump($stmt->errorInfo()); // Debugging
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting TenagaKesehatan: " . $e->getMessage());
            
            throw new \Exception("Database Error TenagaKesehatan: Gagal menghapus tenaga kesehatan: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error deleting TenagaKesehatan: " . $e->getMessage());
           
            throw new \Exception("General Error TenagaKesehatan: Gagal menghapus tenaga kesehatan: " . $e->getMessage());
        }
    }
}