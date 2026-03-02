<?php
declare(strict_types=1);

namespace alt\gateway\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class RoutedProxyAction
{
    private ContainerInterface $container;
    private string $serviceClientKey;
    private string $prefix;

    public function __construct(ContainerInterface $container, string $serviceClientKey, string $prefix = '')
    {
        $this->container = $container;
        $this->serviceClientKey = $serviceClientKey;
        $this->prefix = $prefix;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $client = $this->container->get($this->serviceClientKey);
        
        $path = $request->getUri()->getPath();
        if ($this->prefix && str_starts_with($path, $this->prefix)) {
            $path = substr($path, strlen($this->prefix)) ?: '/';
        }
        
        $options = [
            'query' => $request->getQueryParams(),
            'headers' => [],
        ];
        
        $contentType = $request->getHeaderLine('Content-Type') ?? '';

        if (str_contains($contentType, 'multipart/form-data')) {
            $multipart = [];

            foreach ($request->getParsedBody() as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            $uploadedFiles = $request->getUploadedFiles();
            foreach ($uploadedFiles as $key => $file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => fopen($file->getFilePath(), 'r'),
                        'filename' => $file->getClientFilename()
                    ];
                }
            }

            $options['multipart'] = $multipart;

        } else {
            $data = $request->getParsedBody();
            if (is_array($data) && !empty($data)) {
                $options['json'] = $data;
            }
        }        
        if ($auth = $request->getHeaderLine('Authorization')) {
            $options['headers']['Authorization'] = $auth;
        }

        try {
            $apiResponse = $client->request(
                $request->getMethod(),
                $path,
                $options
            );

            $response->getBody()->write((string) $apiResponse->getBody());
            return $response
                ->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type') ?: 'application/json')
                ->withStatus($apiResponse->getStatusCode());
                
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new HttpNotFoundException($request, "Resource not found");
            }
            
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($e->getResponse()->getStatusCode());
                
        } catch (ServerException $e) {
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($e->getResponse()->getStatusCode());
        }
    }
}