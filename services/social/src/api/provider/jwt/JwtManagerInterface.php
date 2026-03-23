<?php
namespace alt\api\provider\jwt;



interface JwtManagerInterface{
     public const ACCESS_TOKEN = 'access_token';
    public const REFRESH_TOKEN = 'refresh_token';
    
    public function create(array $payload,string $type):string;
    public function validate(string $token): array;
}