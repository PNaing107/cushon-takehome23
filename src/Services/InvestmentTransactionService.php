<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\DAO\AccountDAO;
use App\DataAccess\DAO\InvestmentTransactionDAO;

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
         if(! $this->validateBody($accountUuid, $body)) {
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

    private function validateBody(string $accountUuid, array $body): bool
    {
        if($body['type'] === 'buy') {

            return $this->validatePurchase($accountUuid, $body['transactions']);
        } elseif ($body['type'] === 'sell') {

            return $this->validateSell($accountUuid, $body['transactions']);
        } else {

            $this->responseBody = [
                'message' => 'Invalid transaction type',
                'status' => 400,
                'data' => []
            ];
            
            return false;
        }
    }

    private function validatePurchase(string $accountUuid, array $transactions): bool
    {
        // For now Retail customers can only buy a single fund
        if (count($transactions) > 1) {

            $this->responseBody = [
                'message' => 'You can only purchase one fund',
                'status' => 400,
                'data' => []
            ];

            return false;
        }

        // check they haven't already invested in another fund
        try {
            $data = $this->accessor->getAggregateShares($accountUuid);

            if($data) {

                if($data[0]['symbol'] === $transactions[0]['symbol']) {
                    return true;
                }

                $this->responseBody['message'] = 'You have already invested in another fund.';
                $this->responseBody['status'] = 400;
                return false;

            }
        } catch(\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
            return false;
        }

        return true;
    }

    private function validateSell(string $accountUuid, array $transactions): bool
    {
        // do they have enough to withdraw?
        try {
            $data = $this->accessor->getAggregateShares($accountUuid);

            if(
                !$data || 
                $data[0]['symbol'] !== $transactions[0]['symbol'] ||
                $data[0]['shares'] < abs($transactions[0]['amount'] / $transactions[0]['net_asset_value'])
            ) {
                $this->responseBody['message'] = 'Insufficient funds';
                $this->responseBody['status'] = 400;
                return false;
            }

            return true;
        } catch(\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
            return false;
        }
    }

}