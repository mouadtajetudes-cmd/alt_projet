<?php

namespace alt\core\repositories;

interface MessageRepositoryInterface
{
    public function findByRoom(string $roomId): array;
}
