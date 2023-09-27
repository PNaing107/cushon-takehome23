<?php

declare(strict_types=1);


namespace App\Controllers;


use App\Services\InvestmentTransactionService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class InvestmentTransactionsController
{
    private InvestmentTransactionService $service;

    // Here, the parameter is automatically supplied by the Dependency Injection Container based on the type hint
    public function __construct(InvestmentTransactionService $service)
    {
        $this->service = $service;
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $accountUuid = $args['account_id'];

        // Could add query params for filtering

        $responseBody = $this->service->getAll($accountUuid);

        return $response->withJson($responseBody)->withStatus($responseBody['status']);
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $accountID = $args['account_id'];

        $body = $request->getParsedBody();

        $responseBody = $this->service->store($accountID, $body);

        return $response->withJson($responseBody)->withStatus($responseBody['status']);
    }
}