<?php

namespace alt\core\application\usecases;

use alt\core\application\ports\spi\repositoryInterfaces\LevelRepositoryInterface;
use alt\core\application\ports\api\LevelServiceInterface;

class LevelService implements LevelServiceInterface
{
    private LevelRepositoryInterface $levelRepository;

    public function __construct(LevelRepositoryInterface $levelRepository)
    {
        $this->levelRepository = $levelRepository;
    }

    public function getAllLevels(): array
    {
        return $this->levelRepository->findAll();
    }

    public function getLevelById(int $id_niveau): ?array
    {
        return $this->levelRepository->findById($id_niveau);
    }
}
