<?php

declare(strict_types=1);

namespace App\DataAccess;

use App\Models\Fund;
use App\Models\ResourceCollection;

class FundDAO
{
    public function getAll(): array
    {
        $sql = 'SELECT * FROM `funds` where `deleted_at` IS NULL;';

        $result = Database::getInstance()->fetchAll($sql, [], [\PDO::FETCH_CLASS, Fund::class] );

        
        $resourceCollection = new ResourceCollection;
        
        $resourceCollection->setCollection($result);

        return $result;
    }
}