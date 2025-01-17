<?php

declare(strict_types=1);

namespace App\DataAccess\DAO;

use App\DataAccess\Database;
use App\DataAccess\DAO\DataAccessor;
use App\Models\Account;

class AccountDAO implements DataAccessor
{
    public function getAll(mixed $identifier): array
    {
        $sql = 'SELECT
                `ac`.`id` `id`,
                `ac`.`account_uuid` `account_uuid`,
                `ac`.`customer_id` `customer_id`,
                `at`.`name` `account_type_name`,
                `ac`.`created_at` `created_at`
                FROM `accounts` as `ac`
                LEFT JOIN `account_types` as `at` on `at`.`id` = `ac`.`account_type_id`
                WHERE 1=1
                AND `ac`.`customer_id` = (SELECT `id` FROM `customers` WHERE `uuid` = :uuid)
                AND `ac`.`deleted_at` IS NULL;';

        $result = Database::getInstance()->fetchAll($sql, [$identifier], [\PDO::FETCH_CLASS, Account::class] );

        return $result;
    }

    public function getOne(): array
    {
        //TODO
        return [];
    }

    public function getModelBinding(): string
    {
        return Account::class;
    }

    public function exchangeUuid(string $accountUuid): array
    {

        $sql = "SELECT `id` FROM `accounts` WHERE `account_uuid` = :accountUuid";

        $result = Database::getInstance()->fetchAll($sql, [$accountUuid]);

        return $result;
    }
}