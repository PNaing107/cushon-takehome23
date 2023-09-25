<?php

declare(strict_types=1);

namespace App\Services;

use App\DataAccess\AccountDAO;

class AccountService extends AbstractService
{
    protected AccountDAO $accountDAO;

    protected array $responseBody = [
        'message' => 'Something went wrong.',
        'status' => 500,
        'data' => []
    ];

    public function __construct(AccountDAO $accountDAO)
    {
        $this->accountDAO = $accountDAO;
    }

    public function getAccounts(): array
    {
        try {
            $accounts = $this->accountDAO->getAll();
        } catch (\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
        }

        if (isset($accounts) && $accounts) {

            $this->responseBody = [
                'message' => 'Accounts successfully retrieved.',
                'status' => 200,
                'data' => $accounts
            ];
        }

        return $this->responseBody;
    }
}