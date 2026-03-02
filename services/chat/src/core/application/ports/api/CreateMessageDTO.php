<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class CreateMessageDTO
{
    public string $conversationId;
    public string $senderId;
    public string $content;
    public string $type = 'text';
}
