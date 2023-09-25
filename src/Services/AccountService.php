<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\DAO\AccountDAO;

class AccountService extends AbstractService
{
    public function __construct(AccountDAO $accountDAO)
    {
        parent::__construct($accountDAO);
    }
}