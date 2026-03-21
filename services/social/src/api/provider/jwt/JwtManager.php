<?php

namespace alt\api\provider\jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtManager implements JwtManagerInterface{
    private string $secret='341e24419bac01ddffd0964991bc701b';
    private int $access_expiration_time=3600;
    private int $refresh_expiration_time=604800;
    private string $issuer="toubilib";

    public function __construct(string $secret='341e24419bac01ddffd0964991bc701b'
    ,int $access_expiration_time=3600,int $refresh_expiration_time=604800
    ,string $issuer="toubilib"){
        $this->secret = $secret?? $_ENV['JWT_SECRET'];
        $this->access_expiration_time =$access_expiration_time?? (int) $_ENV['JWT_ACCESS_EXPIRATION'];
        $this->refresh_expiration_time = $refresh_expiration_time?? (int) $_ENV['JWT_REFRESH_EXPIRATION'];
        $this->issuer =$issuer ?? $_ENV['JWT_ISSUER'];
    }
    public function create(array $payload,string $type):string{
        if($type===JwTManagerInterface::ACCESS_TOKEN){
            $expTime=time()+$this->access_expiration_time;
        }else{
            $expTime=time()+$this->refresh_expiration_time;
        }
        $token=JWT::encode([
            'iss'=>$this->issuer,
            'iat'=>time(),
            'sub'=>$payload['id'],
            'exp'=>$expTime,
            'upr'=>$payload
        ],$this->secret, 'HS256');
        return $token;
    }
public function validate(string $token): array
{
    try {
        $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));

        $userPayload = isset($decoded->upr) ? (array) $decoded->upr : [];

        return [
            'userId' => $decoded->sub,
            'email' => $userPayload['email'] ?? null,
            'administrateur' => $userPayload['isAdmin'] ?? null,
        ];

    } catch (\Exception $e) {
        return [];
    }

}}