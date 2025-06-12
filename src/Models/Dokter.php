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

    public function createDokter(array $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO dokter (
                    id_nakes, nip, nama_dokter, status,
                    str, spesialisasi, no_hp
                ) VALUES (
                    :id_nakes, :nip, :nama_dokter, :status,
                    :str, :spesialisasi, :no_hp
                )"
            );

            // Mapping dari camelCase GraphQL ke snake_case database
            $stmt->bindParam(':id_nakes', $data['idNakes']);
            $stmt->bindParam(':nip', $data['nip']);
            $stmt->bindParam(':nama_dokter', $data['namaDokter']); // Menggunakan namaDokter dari GraphQL
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':str', $data['str']);
            $stmt->bindParam(':spesialisasi', $data['spesialisasi']);
            $stmt->bindParam(':no_hp', $data['noHp']);

            $stmt->execute();
            return $this->getDokterById((int)$this->pdo->lastInsertId());
        } catch (PDOException $e) {
            error_log("Error creating dokter: " . $e->getMessage());
            return null;
        }
    }

    public function getDokterById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dokter WHERE id_dokter = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllDokter()
    {
        $stmt = $this->pdo->query("SELECT * FROM dokter");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDokter(int $id, array $data)
    {
        try {
            $setParts = [];
            $bindValues = [];
            foreach ($data as $key => $value) {
                // Konversi camelCase GraphQL arg ke snake_case kolom database
                // Perlakuan khusus untuk namaDokter menjadi nama_dokter
                if ($key === 'namaDokter') {
                    $dbColumn = 'nama_dokter';
                } elseif ($key === 'idNakes') { // Perlakuan khusus untuk idNakes menjadi id_nakes
                    $dbColumn = 'id_nakes';
                } else {
                    $dbColumn = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
                }
                $setParts[] = "$dbColumn = :$key";
                $bindValues[":$key"] = $value;
            }
            $setClause = implode(', ', $setParts);

            if (empty($setClause)) {
                return $this->getDokterById($id);
            }

            $sql = "UPDATE dokter SET $setClause WHERE id_dokter = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            foreach ($bindValues as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();
            return $this->getDokterById($id);
        } catch (PDOException $e) {
            error_log("Error updating dokter (ID: $id): " . $e->getMessage());
            return null;
        }
    }

    public function deleteDokter(int $id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM dokter WHERE id_dokter = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting dokter (ID: $id): " . $e->getMessage());
            return false;
        }
    }
}