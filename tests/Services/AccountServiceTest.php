<?php

namespace Tests\Services;

use App\DataAccess\DAO\AccountDAO;
use App\Models\Account;
use App\Services\AccountService;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    public function test_getAll_throws_response_given_PDO_error()
    {
        // Arrange
        $accessorMock = $this->createMock(AccountDAO::class);

        $accessorMock->method('getAll')
                    ->willThrowException(new \PDOException('Mocked PDO Exception'));

        // Act
        $result = (new AccountService($accessorMock))->getAll('abc-123');

        // Assert
        $expected = [
            'message' => 'Mocked PDO Exception',
            'status' => 500,
            'data' => []
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_getAll_returns_response()
    {
        // Arrange
        $accounts = [
            [
                'id' => 1,
                'customer_id' => 1,
                'account_type' => 'Testing Account',
                'created_at' => '2023-09-27 11:22:11'
            ]
        ];

        $accessorMock = $this->createConfiguredMock(AccountDAO::class, [
            'getModelBinding' => Account::class,
            'getAll' => $accounts,
        ]);

        // Act
        $result = (new AccountService($accessorMock))->getAll('abc-123');

        // Assert
        $expected = [
            'message' => 'Accounts successfully retrieved.',
            'status' => 200,
            'data' => $accounts
        ];

        $this->assertEquals($expected, $result);
    }
}