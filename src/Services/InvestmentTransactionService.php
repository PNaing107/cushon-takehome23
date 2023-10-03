<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\DAO\AccountDAO;
use App\DataAccess\DAO\InvestmentTransactionDAO;
use App\Services\Validation\InvestmentTransactionValidator;

class InvestmentTransactionService extends AbstractService
{
    private AccountDAO $accountAccessor;

    public function __construct(InvestmentTransactionDAO $investmentTransactionDAO, AccountDAO $accountDAO)
    {
        parent::__construct($investmentTransactionDAO);
        $this->accountAccessor = $accountDAO;
    }

    public function store(string $accountUuid, array $body)
    {
         if(! InvestmentTransactionValidator::validatePostRequest($accountUuid, $body, $this->accessor)) {
            $this->responseBody['status'] = InvestmentTransactionValidator::getStatusCode();
            $this->responseBody['message'] = InvestmentTransactionValidator::getErrorMessage();
            return $this->responseBody;
        }

        // post the transaction and return 201 response
        try {

            $accountId = $this->accountAccessor->exchangeUuid($accountUuid);

            $this->accessor->store($body['transactions'], $accountId[0]['id']);

            return [
                'message' => 'Transaction complete',
                'status' => 201,
                'data' => []
            ];

        } catch (\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
        }

        return $this->responseBody;
         
    }

}