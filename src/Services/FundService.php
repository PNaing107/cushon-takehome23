<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\DAO\FundDAO;

class FundService extends AbstractService
{

    public function __construct(FundDAO $fundDAO)
    {
        parent::__construct($fundDAO);
    }

}