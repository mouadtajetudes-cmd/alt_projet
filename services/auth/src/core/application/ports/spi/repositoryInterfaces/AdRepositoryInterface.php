<?php
declare(strict_types=1);

namespace alt\core\application\ports\spi\repositoryInterfaces;

use alt\core\domain\entities\Ad;

interface AdRepositoryInterface
{
    public function findAll(): array;
    public function findById(int $id): Ad;
    public function create(Ad $ad): Ad;
}
