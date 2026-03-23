<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class CreateConversationDTO
{
    public string $name;
    public array $participants = [];
    public string $type = 'private';
}
