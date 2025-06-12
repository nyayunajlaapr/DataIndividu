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

    public function createTenagaKesehatan(array $data)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tenaga_kesehatan (
                    nip, nama_lengkap, jenis_kelamin, tanggal_lahir, no_hp,
                    no_bpjs, status_pernikahan, pekerjaan, role
                ) VALUES (
                    :nip, :nama_lengkap, :jenis_kelamin, :tanggal_lahir, :no_hp,
                    :no_bpjs, :status_pernikahan, :pekerjaan, :role
                )"
            );

            $stmt->bindParam(':nip', $data['nip']);
            $stmt->bindParam(':nama_lengkap', $data['namaLengkap']);
            $stmt->bindParam(':jenis_kelamin', $data['jenisKelamin']);
            $stmt->bindParam(':tanggal_lahir', $data['tanggalLahir']);
            $stmt->bindParam(':no_hp', $data['noHp']);
            $stmt->bindParam(':no_bpjs', $data['noBpjs']);
            $stmt->bindParam(':status_pernikahan', $data['statusPernikahan']);
            $stmt->bindParam(':pekerjaan', $data['pekerjaan']);
            $stmt->bindParam(':role', $data['role']);

            $stmt->execute();
            return $this->getTenagaKesehatanById((int)$this->pdo->lastInsertId());
        } catch (PDOException $e) {
            error_log("Error creating tenaga kesehatan: " . $e->getMessage());
            return null;
        }
    }

    public function getTenagaKesehatanById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tenaga_kesehatan WHERE id_nakes = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllTenagaKesehatan()
    {
        $stmt = $this->pdo->query("SELECT * FROM tenaga_kesehatan");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTenagaKesehatan(int $id, array $data)
    {
        try {
            $setParts = [];
            $bindValues = [];
            foreach ($data as $key => $value) {
                $dbColumn = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
                $setParts[] = "$dbColumn = :$key";
                $bindValues[":$key"] = $value;
            }
            $setClause = implode(', ', $setParts);

            if (empty($setClause)) {
                return $this->getTenagaKesehatanById($id);
            }

            $sql = "UPDATE tenaga_kesehatan SET $setClause WHERE id_nakes = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            foreach ($bindValues as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            $stmt->execute();
            return $this->getTenagaKesehatanById($id);
        } catch (PDOException $e) {
            error_log("Error updating tenaga kesehatan (ID: $id): " . $e->getMessage());
            return null;
        }
    }

    public function deleteTenagaKesehatan(int $id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tenaga_kesehatan WHERE id_nakes = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting tenaga kesehatan (ID: $id): " . $e->getMessage());
            return false;
        }
    }
}