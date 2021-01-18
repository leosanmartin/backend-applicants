<?php

namespace Osana\Challenge\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VersionController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $data = [
            'name' => env('API_NAME'),
            'version' => env('API_VERSION')
        ];

        $response->getBody()->write(json_encode($data));

        return $response->withStatus(200, 'OK')
            ->withHeader('Content-Type', 'application/json');
    }
}
