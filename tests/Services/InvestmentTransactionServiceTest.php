<?php

namespace Tests\Services;

use App\DataAccess\DAO\AccountDAO;
use App\DataAccess\DAO\InvestmentTransactionDAO;
use App\Models\InvestmentTransaction;
use App\Services\InvestmentTransactionService;
use App\Services\Validation\InvestmentTransactionValidator;
use \Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tests\TestCase;

class InvestmentTransactionServiceTest extends MockeryTestCase
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

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->getAll('abc-123');

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

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => true
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->getAll('abc-123');

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

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => false,
            'getStatusCode' => 406,
            'getErrorMessage' => 'Invalid Transaction: Multiple Transactions not allowed'
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

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

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => false,
            'getStatusCode' => 406,
            'getErrorMessage' => 'Invalid Transaction: You have already invested in another fund.'
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

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

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => true
        ]);

        // Act
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

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
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => -123.45
                ]
            ]
        ];

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => true
        ]);

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
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Transaction complete',
            'status' => 201,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

    public function test_store_returns_correct_response_when_body_contains_invalid_sell_transaction()
    {
        // Arrange
        $body = [
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => 0
                ]
            ]
        ];

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => false,
            'getStatusCode' => 406,
            'getErrorMessage' => 'Invalid Transaction: Transaction amount of 0 is not allowed'
        ]);

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
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Invalid Transaction: Transaction amount of 0 is not allowed',
            'status' => 406,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

    public function test_store_returns_correct_response_when_body_contains_insufficient_sell_transaction()
    {
        // Arrange
        $body = [
            'transactions' => [
                [
                    'id' => 1,
                    'symbol' => 'TEST',
                    'net_asset_value' => 123.45,
                    'amount' => -2000
                ]
            ]
        ];

        $validatorMock = Mockery::mock('overload:' . InvestmentTransactionValidator::class, [
            'validatePostRequest' => false,
            'getStatusCode' => 406,
            'getErrorMessage' => 'Invalid Transaction: Insufficient funds'
        ]);

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
        $result = (new InvestmentTransactionService($accessorMock, $this->accountDAOMock, $validatorMock))->store('abc-123', $body);

        // Assert
        $expected = [
            'message' => 'Invalid Transaction: Insufficient funds',
            'status' => 406,
            'data' => []
        ];

        $this->assertEquals($expected, $result);

    }

}