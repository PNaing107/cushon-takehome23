<?php

declare(strict_types=1);


namespace App\Controllers;


use App\Services\AccountService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AccountsController
{
    private AccountService $service;

    // Here, the parameter is automatically supplied by the Dependency Injection Container based on the type hint
    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function index(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $responseBody = $this->service->getAccounts();

        return $response->withJson($responseBody)->withStatus($responseBody['status']);
    }
}