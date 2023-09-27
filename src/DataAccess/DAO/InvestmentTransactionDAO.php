<?php

declare(strict_types=1);

namespace App\DataAccess\DAO;

use App\DataAccess\Database;
use App\Models\InvestmentTransaction;

class InvestmentTransactionDAO implements DataAccessor
{
    public function getAll(mixed $accountUuid): array
    {
        $sql = 'SELECT
                `it`.`id`,
                `it`.`amount`,
                `f`.`symbol`,
                `f`.`name`,
                `it`.`net_asset_value`,
                `it`.`created_at`
                FROM `investment_transactions` as `it`
                INNER JOIN `funds` as `f` on `f`.`id` = `it`.`fund_id` 
                WHERE `it`.`account_id` = (SELECT `id` FROM `accounts` WHERE `account_uuid` = :accountUuiD);';

        $result = Database::getInstance()->fetchAll($sql, [$accountUuid], [\PDO::FETCH_CLASS, InvestmentTransaction::class] );

        return $result;
    }

    public function getOne(): array
    {
        //TODO
        return [];
    }

    public function getAggregateShares(string $accountUuid): array
    {
        $sql = 'SELECT 
                `f`.`symbol` as `symbol`, 
                SUM(`it`.`shares`) as `shares`
                FROM `investment_transactions` as `it`
                INNER JOIN `funds` as `f` on `f`.`id` = `it`.`fund_id`
                WHERE `it`.`account_id` = (SELECT `id` FROM `accounts` WHERE `account_uuid` = :accountUuid) 
                GROUP BY `f`.`symbol`
                HAVING `shares` > 0';

        $result = Database::getInstance()->fetchAll($sql, [$accountUuid]);

        return $result;
    }

    public function store(array $transactions, int $accountId)
    {
        // for now we only handle a single transaction per request
        $fundId = $transactions[0]['id'];
        $amount = $transactions[0]['amount'];
        $netAssetValue = $transactions[0]['net_asset_value'];

        $sql = 'INSERT INTO `investment_transactions` 
                (`account_id`, `fund_id`, `amount`, `net_asset_value`) 
                VALUES 
                (:accountId, :fundId, :amount, :netAssetValue)';

        Database::getInstance()->fetch($sql, [$accountId, $fundId, $amount,  $netAssetValue]);

    }

    public function getModelBinding(): string
    {
        return InvestmentTransaction::class;
    }

}