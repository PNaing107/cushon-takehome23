<?php

declare(strict_types=1);

namespace App\DataAccess\DAO;

use App\DataAccess\Database;
use App\Models\Fund;

class FundDAO implements DataAccessor
{
    public function getAll(mixed $identifier): array
    {
        $sql = 'SELECT * FROM `funds` where `deleted_at` IS NULL;';

        $result = Database::getInstance()->fetchAll($sql, [], [\PDO::FETCH_CLASS, Fund::class] );

        return $result;
    }

    public function getOne(): array
    {
        //TODO
        return [];
    }

    public function getModelBinding(): string
    {
        return Fund::class;
    }

}