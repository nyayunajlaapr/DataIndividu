<?php
// src/Models/Dokter.php

namespace Iae\LayananDataIndividu\Models;

use PDO;
use PDOException;

class Dokter
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mengambil satu data dokter berdasarkan ID.
     * @param int $id ID Dokter
     * @return array|false
     */
    public function getDokterById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id_dokter, id_nakes, nip, nama_dokter, status, str, spesialisasi, no_hp FROM dokter WHERE id_dokter = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching Dokter by ID: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil data dokter.");
        }
    }

    /**
     * Mengambil semua data dokter.
     * @return array
     */
    public function getAllDokter()
    {
        try {
            $stmt = $this->pdo->query("SELECT id_dokter, id_nakes, nip, nama_dokter, status, str, spesialisasi, no_hp FROM dokter ORDER BY id_dokter ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all Dokter: " . $e->getMessage());
            throw new \Exception("Database Error: Gagal mengambil semua data dokter.");
        }
    }

    /**
     * Membuat data dokter baru.
     * @param array $data Data dokter yang akan disimpan.
     * @return array|null Data dokter yang baru dibuat, atau null jika gagal.
     */
    public function createDokter(array $data)
    {
        try {
            $sql = "INSERT INTO dokter (id_nakes, nip, nama_dokter, status, str, spesialisasi, no_hp)
                    VALUES (:id_nakes, :nip, :nama_dokter, :status, :str, :spesialisasi, :no_hp)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':id_nakes', $data['idNakes'] ?? null, PDO::PARAM_INT); // GraphQL uses camelCase, DB uses snake_case
            $stmt->bindValue(':nip', $data['nip'] ?? null);
            $stmt->bindValue(':nama_dokter', $data['namaDokter'] ?? null);
            $stmt->bindValue(':status', $data['status'] ?? null);
            $stmt->bindValue(':str', $data['str'] ?? null);
            $stmt->bindValue(':spesialisasi', $data['spesialisasi'] ?? null);
            $stmt->bindValue(':no_hp', $data['noHp'] ?? null);

            $executeResult = $stmt->execute();

            if ($executeResult) {
                $lastId = $this->pdo->lastInsertId();
                if ($lastId) {
                    return $this->getDokterById((int)$lastId);
                }
            }
            return null;

        } catch (PDOException $e) {
            error_log("Error creating Dokter: " . $e->getMessage());
            var_dump("PDOException caught in createDokter: " . $e->getMessage()); // For immediate debug in terminal
            throw new \Exception("Database Error Dokter: Gagal membuat dokter baru: " . $e->getMessage());
        } catch (\Exception $e) {
             error_log("General Error creating Dokter: " . $e->getMessage());
             var_dump("General Exception caught in createDokter: " . $e->getMessage()); // For immediate debug in terminal
             throw new \Exception("General Error Dokter: Gagal membuat dokter baru: " . $e->getMessage());
        }
    }

    /**
     * Memperbarui data dokter yang sudah ada.
     * @param int $id ID Dokter
     * @param array $data Data dokter yang akan diperbarui.
     * @return array|null Data dokter yang diperbarui, atau null jika gagal.
     */
    public function updateDokter($id, array $data)
    {
        try {
            $setClauses = [];
            $bindValues = [];

            // Mapping GraphQL camelCase ke kolom database snake_case
            $fieldMap = [
                'idNakes' => 'id_nakes',
                'nip' => 'nip',
                'namaDokter' => 'nama_dokter',
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
                return $this->getDokterById($id); // Tidak ada yang diupdate
            }

            $sql = "UPDATE dokter SET " . implode(', ', $setClauses) . " WHERE id_dokter = :id";
            $stmt = $this->pdo->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            foreach ($bindValues as $param => $value) {
                // Determine param type for idNakes if it's explicitly null/int
                if ($param === ':idNakes' && ($value === null || is_int($value))) {
                    $stmt->bindValue($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value);
                }
            }

            $stmt->execute();
            return $this->getDokterById($id);

        } catch (PDOException $e) {
            error_log("Error updating Dokter: " . $e->getMessage());
            var_dump("PDOException caught in updateDokter: " . $e->getMessage()); // For immediate debug in terminal
            throw new \Exception("Database Error Dokter: Gagal memperbarui data dokter: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error updating Dokter: " . $e->getMessage());
            var_dump("General Exception caught in updateDokter: " . $e->getMessage()); // For immediate debug in terminal
            throw new \Exception("General Error Dokter: Gagal memperbarui data dokter: " . $e->getMessage());
        }
    }

    /**
     * Menghapus data dokter berdasarkan ID.
     * @param int $id ID Dokter
     * @return bool True jika berhasil dihapus, false jika tidak.
     */
    public function deleteDokter($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM dokter WHERE id_dokter = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting Dokter: " . $e->getMessage());
            var_dump("PDOException caught in deleteDokter: " . $e->getMessage()); // For immediate debug in terminal
            throw new \Exception("Database Error Dokter: Gagal menghapus dokter: " . $e->getMessage());
        } catch (\Exception $e) {
            error_log("General Error deleting Dokter: " . $e->getMessage());
            var_dump("General Exception caught in deleteDokter: " . $e->getMessage()); // For immediate debug in terminal
            throw new \Exception("General Error Dokter: Gagal menghapus dokter: " . $e->getMessage());
        }
    }
}