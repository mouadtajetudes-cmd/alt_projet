<?php

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    'auth.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.auth:80',
        ]);
    },
    'avatar.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.avatar:80',
        ]);
    },
    'chat.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.chat:80',
        ]);
    },
    'marketplace.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.marketplace:80',
        ]);
    },
    'social.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.social:80',
        ]);
    },
];
