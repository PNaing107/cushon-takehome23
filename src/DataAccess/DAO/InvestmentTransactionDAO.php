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
                LEFT JOIN `funds` as `f` on `f`.`id` = `it`.`fund_id` 
                WHERE `it`.`account_id` = (SELECT `id` FROM `accounts` WHERE `investment_account_uuid` = :accountUuiD);';

        $result = Database::getInstance()->fetchAll($sql, [$accountUuid], [\PDO::FETCH_CLASS, InvestmentTransaction::class] );

        return $result;
    }

    public function getOne(): array
    {
        //TODO
        return [];
    }

    public function getModelBinding(): string
    {
        return InvestmentTransaction::class;
    }

}