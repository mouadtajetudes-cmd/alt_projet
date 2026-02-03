<?php

namespace alt\core\services;

interface MessageServiceInterface
{
    public function getMessagesByRoom(string $roomId): array;
}
