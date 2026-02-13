<?php
declare(strict_types=1);

namespace alt\core\application\ports\api;

class LoginDTO
{
    public string $email;
    public string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}
