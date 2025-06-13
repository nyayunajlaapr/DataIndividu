<?php
// src/Models/Perawat.php

namespace Iae\LayananDataIndividu\Models;

use PDO;
use PDOException;

class Perawat
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil satu data perawat berdasarkan ID.
     * @param int $id ID Perawat
     * @return array|false
     */
    public function getPerawatById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id_perawat, id_nakes, nip, nama_perawat, status, str, spesialisasi, no_hp FROM perawat WHERE id_perawat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching Perawat by ID: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil data perawat.");
        }
    }

    /**
     * Mengambil semua data perawat.
     * @return array
     */
    public function getAllPerawat()
    {
        try {
            $stmt = $this->pdo->query("SELECT id_perawat, id_nakes, nip, nama_perawat, status, str, spesialisasi, no_hp FROM perawat ORDER BY id_perawat ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all Perawat: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil semua data perawat.");
        }
    }

    /**
     * Membuat data perawat baru.
     * @param array $data Data perawat yang akan disimpan.
     * @return array|null Data perawat yang baru dibuat, atau null jika gagal.
     */
    public function createPerawat(array $data)
    {
        try {
            $sql = "INSERT INTO perawat (id_nakes, nip, nama_perawat, status, str, spesialisasi, no_hp)
                    VALUES (:id_nakes, :nip, :nama_perawat, :status, :str, :spesialisasi, :no_hp)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':id_nakes', $data['idNakes'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':nip', $data['nip'] ?? null);
            $stmt->bindValue(':nama_perawat', $data['namaPerawat'] ?? null);
            $stmt->bindValue(':status', $data['status'] ?? null);
            $stmt->bindValue(':str', $data['str'] ?? null);
            $stmt->bindValue(':spesialisasi', $data['spesialisasi'] ?? null);
            $stmt->bindValue(':no_hp', $data['noHp'] ?? null);

            $executeResult = $stmt->execute();

            if ($executeResult) {
                $lastId = $this->pdo->lastInsertId();
                if ($lastId) {
                    return $this->getPerawatById((int)$lastId);
                }
            }
            return null;

        } catch (PDOException $e) {
            error_log("Error creating Perawat: " . $e->getMessage());
            var_dump("PDOException caught in createPerawat: " . $e->getMessage());
            throw new \Exception("Database Error Perawat: Gagal membuat perawat baru: " . $e->getMessage());
        } catch (\Exception $e) {
             error_log("General Error creating Perawat: " . $e->getMessage());
             var_dump("General Exception caught in createPerawat: " . $e->getMessage());
             throw new \Exception("General Error Perawat: Gagal membuat perawat baru: " . $e->getMessage());
        }
    }

    /**
     * Memperbarui data perawat yang sudah ada.
     * @param int $id ID Perawat
     * @param array $data Data perawat yang akan diperbarui.
     * @return array|null Data perawat yang diperbarui, atau null jika gagal.
     */
    public function updatePerawat($id, array $data)
    {
        try {
            $setClauses = [];
            $bindValues = [];

            // Mapping GraphQL camelCase ke kolom database snake_case
            $fieldMap = [
                'idNakes' => 'id_nakes',
                'nip' => 'nip',
                'namaPerawat' => 'nama_perawat',
                'status' => 'status',
                'str' => 'str',
                'spesialisasi' => 'spesialisasi',
                'noHp' => 'no_hp',
            ];

            foreach ($data as $key => $value) {
                if (isset($fieldMap[$key])) {
                    $dbColumn = $fieldMap[$key];
                    $setClauses[] = "$dbColumn = :$key";
                    $bindValues[":$key"] = $value;
                }
            }

            if (empty($setClauses)) {
                return $this->getPerawatById($id); // Tidak ada yang diupdate
            }

            $sql = "UPDATE perawat SET " . implode(', ', $setClauses) . " WHERE id_perawat = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($bindValues as $param => $value) {
                if ($param === ':idNakes' && ($value === null || is_int($value))) {
                    $stmt->bindValue($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value);
                }
            }

            $stmt->execute();
            return $this->getPerawatById($id);

        } catch (PDOException $e) {
            error_log("Error updating Perawat: " . $e->getMessage());
            var_dump("PDOException caught in updatePerawat: " . $e->getMessage());
            throw new \Exception("Database Error Perawat: Gagal memperbarui data perawat: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error updating Perawat: " . $e->getMessage());
            var_dump("General Exception caught in updatePerawat: " . $e->getMessage());
            throw new \Exception("General Error Perawat: Gagal memperbarui data perawat: " . $e->getMessage());
        }
    }

    /**
     * Menghapus data perawat berdasarkan ID.
     * @param int $id ID Perawat
     * @return bool True jika berhasil dihapus, false jika tidak.
     */
    public function deletePerawat($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM perawat WHERE id_perawat = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting Perawat: " . $e->getMessage());
            var_dump("PDOException caught in deletePerawat: " . $e->getMessage());
            throw new \Exception("Database Error Perawat: Gagal menghapus perawat: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error deleting Perawat: " . $e->getMessage());
            var_dump("General Exception caught in deletePerawat: " . $e->getMessage());
            throw new \Exception("General Error Perawat: Gagal menghapus perawat: " . $e->getMessage());
        }
    }
}