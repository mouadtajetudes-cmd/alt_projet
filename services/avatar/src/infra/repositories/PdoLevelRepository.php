<?php

namespace alt\infra\repositories;

use alt\core\application\ports\spi\repositoryInterfaces\LevelRepositoryInterface;
use PDO;

class PdoLevelRepository implements LevelRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM niveaux');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id_niveau): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM niveaux WHERE id_niveau = :id_niveau');
        $stmt->execute(['id_niveau' => $id_niveau]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}