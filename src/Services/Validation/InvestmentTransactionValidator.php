<?php

namespace App\Services\Validation;

use App\DataAccess\DAO\DataAccessor;

class InvestmentTransactionValidator
{
    protected static $accessor;

    protected static string $errorMessage;

    protected static int $statusCode;

    public static function validatePostRequest(string $accountUuid, array $body, DataAccessor $accessor): bool
    {
        static::$accessor = $accessor;

        // For now Retail customers can only make a single transaction at a time
        if(count($body['transactions']) > 1) {
            static::$statusCode = 406;
            static::$errorMessage = 'Invalid Transaction: Multiple Transactions not allowed';

            return false;
        }

        if($body['transactions'][0]['amount'] == 0) {
                static::$statusCode = 406;
                static::$errorMessage = 'Invalid Transaction: Transaction amount of 0 is not allowed';
            return false;
        }

        if($body['transactions'][0]['amount'] > 0) {
            return static::validatePurchase($accountUuid, $body['transactions'][0]);
        } else {
            return static::validateSell($accountUuid, $body['transactions'][0]);
        }


    }

    protected static function validatePurchase(string $accountUuid, array $transaction): bool
    {
        // check they haven't already invested in another fund
        try {
            $data = static::$accessor->getAggregateShares($accountUuid);

            if($data) {

                if($data[0]['symbol'] === $transaction['symbol']) {
                    return true;
                }
                static::$statusCode = 406;
                static::$errorMessage = 'Invalid Transaction: You have already invested in another fund.';
                return false;

            }
        } catch(\PDOException $exception) {
            static::$statusCode = 500;
            static::$errorMessage = $exception->getMessage();
            return false;
        }

        return true;
    }

    protected static function validateSell(string $accountUuid, array $transaction): bool
    {
        // do they have enough to withdraw?
        try {
            $data = static::$accessor->getAggregateShares($accountUuid);
            if(
                !$data || 
                $data[0]['symbol'] !== $transaction['symbol'] ||
                $data[0]['shares'] < abs($transaction['amount'] / $transaction['net_asset_value'])
            ) {
                static::$statusCode = 406;
                static::$errorMessage = 'Invalid Transaction: Insufficient funds';
                return false;
            }

            return true;
        } catch(\PDOException $exception) {
            static::$statusCode = 500;
            static::$errorMessage = $exception->getMessage();
            return false;
        }
    }

    public static function getErrorMessage(): string
    {
        return static::$errorMessage;
    }

    public static function getStatusCode(): int
    {
        return static::$statusCode;
    }

}