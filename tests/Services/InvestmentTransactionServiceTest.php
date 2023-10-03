<?php

namespace Tests\Services;

use App\DataAccess\DAO\AccountDAO;
use App\DataAccess\DAO\InvestmentTransactionDAO;
use App\Models\InvestmentTransaction;
use App\Services\InvestmentTransactionService;
use Tests\TestCase;

class InvestmentTransactionServiceTest extends TestCase
{
    private $accountDAOMock;

    public function setup(): void
    {
        $this->accountDAOMock = $this->createConfiguredMock(AccountDAO::class, [
            'exchangeUuid' => [['id'=>1]],
        ]);
    }

    public function test_getAll_throws_response_given_PDO_error()
    {
        // Arrange
        $accessorMock = $this->createMock(InvestmentTransactionDAO::class);

        $accessorMock->method('getAll')
                    ->willThrowException(new \PDOException('Mocked PDO Exception'));

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->getAll('abc-123');

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
        $investmentTransactions = [
            [
                'id' => 1,
                'amount' => 123.45,
                'symbol' => 'TEST',
                'name' => 'Testing Fund',
                'net_asset_value' => 123.45,
                'created_at' => '2023-09-27 11:22:11'
            ]
        ];

        $accessorMock = $this->createConfiguredMock(InvestmentTransactionDAO::class, [
            'getModelBinding' => InvestmentTransaction::class,
            'getAll' => $investmentTransactions,
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->getAll('abc-123');

        // Assert
        $expected = [
            'message' => 'InvestmentTransactions successfully retrieved.',
            'status' => 200,
            'data' => $investmentTransactions
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_store_returns_correct_response_when_body_contains_multiple_buy_transactions()
    {
        // Arrange
        $body = [
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => 123.45
                ],
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => 123.45
                ]
            ]
        ];

        $accountDAOMock = $this->createConfiguredMock(AccountDAO::class, [
            'exchangeUuid' => [1],
        ]);

        $accessorMock = $this->createConfiguredMock(InvestmentTransactionDAO::class, [
            'getAggregateShares' => [
                [
                    'symbol' => 'TEST',
                    'shares' => 1,
                ]
            ],
            'store' => null,
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Invalid Transaction: Multiple Transactions not allowed',
            'status' => 406,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

    public function test_store_returns_correct_response_when_body_contains_invalid_buy_transaction()
    {
        // Arrange
        $body = [
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => 123.45
                ]
            ]
        ];

        $accountDAOMock = $this->createConfiguredMock(AccountDAO::class, [
            'exchangeUuid' => [1],
        ]);

        $accessorMock = $this->createConfiguredMock(InvestmentTransactionDAO::class, [
            'getAggregateShares' => [
                [
                    'symbol' => 'TEST2',
                    'shares' => 1,
                ]
            ],
            'store' => null,
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Invalid Transaction: You have already invested in another fund.',
            'status' => 406,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

    public function test_store_returns_correct_response_when_body_contains_valid_buy_transaction()
    {
        // Arrange
        $body = [
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => 123.45
                ]
            ]
        ];

        $accessorMock = $this->createConfiguredMock(InvestmentTransactionDAO::class, [
            'getAggregateShares' => [
                [
                    'symbol' => 'TEST',
                    'shares' => 1,
                ]
            ],
            'store' => 1,
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Transaction complete',
            'status' => 201,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

    public function test_store_returns_correct_response_when_body_contains_valid_sell_transaction()
    {
        // Arrange
        $body = [
            'type' => 'sell',
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => -123.45
                ]
            ]
        ];

        $accessorMock = $this->createConfiguredMock(InvestmentTransactionDAO::class, [
            'getAggregateShares' => [
                [
                    'symbol' => 'TEST',
                    'shares' => 1,
                ]
            ],
            'store' => 1,
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Transaction complete',
            'status' => 201,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

}