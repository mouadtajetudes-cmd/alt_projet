<?php
namespace alt\api\actions;

use Psr\Http\Message\ResponseInterface;

class JsonError{
    public function jsonError(
        ResponseInterface $response,
        string $message,
        int $status
    ): ResponseInterface {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $message
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

}