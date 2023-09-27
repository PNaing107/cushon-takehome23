<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\DAO\AccountDAO;
use App\DataAccess\DAO\InvestmentTransactionDAO;
use PhpParser\Node\Stmt\TryCatch;

class InvestmentTransactionService extends AbstractService
{

    public function __construct(InvestmentTransactionDAO $investmentTrnsactionDAO)
    {
        parent::__construct($investmentTrnsactionDAO);
    }

    public function store(string $accountUuid, array $body)
    {
         if(! $this->validateBody($accountUuid, $body)) {
            return $this->responseBody;
        }

        // post the transaction and return 201 response
        try {

            $accountId = (new AccountDAO)->exchangeUuid($accountUuid, 'investment');

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

            return $this->validateSell($accountUuid, $body);
        } else {

            $this->responseBody = [
                'message' => 'Invalid transaction type',
                'status' => 400,
                'data' => []
            ];
            
            return false;
        }

        //to remove
        return true;
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

                $this->responseBody['message'] = 'You have already invested in another fund. ';
                $this->responseBody['status'] = 400;
                return false;

            }
        } catch(\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
            return false;
        }


    }


            // deposit validator
                //sum all transactions by fund id, if two rows are returned reject, if one row returned by it doesn't match the incoming transaction reject
            // withdraw validator
                // do they have enough to withdraw?
        // 1. multiple transaction not allowed at the moment
    

    private function validateSell(string $accountUuid, array $body): bool
    {
        return false;
    }

}