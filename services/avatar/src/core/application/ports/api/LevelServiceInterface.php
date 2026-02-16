<?php

namespace alt\core\application\ports\api;

interface LevelServiceInterface
{
    public function getAllLevels(): array;
    public function getLevelById(int $id_niveau): ?array;
}
