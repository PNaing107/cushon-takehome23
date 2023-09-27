<?php

namespace Tests\Services;

use App\DataAccess\DAO\FundDAO;
use App\Models\Fund;
use App\Services\FundService;
use Tests\TestCase;

class FundServiceTest extends TestCase
{
    public function test_getAll_throws_response_given_PDO_error()
    {
        // Arrange
        $accessorMock = $this->createMock(FundDAO::class);

        $accessorMock->method('getAll')
                    ->willThrowException(new \PDOException('Mocked PDO Exception'));

        // Act
        $result = (new FundService($accessorMock))->getAll('abc-123');

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
        $funds = [
            [
                'id' => 1,
                'symbol' => 'TEST',
                'name' => 'Testing Fund'
            ]
        ];

        $accessorMock = $this->createConfiguredMock(FundDAO::class, [
            'getModelBinding' => Fund::class,
            'getAll' => $funds,
        ]);

        // Act
        $result = (new FundService($accessorMock))->getAll('abc-123');

        // Assert
        $expected = [
            'message' => 'Funds successfully retrieved.',
            'status' => 200,
            'data' => $funds
        ];

        $this->assertEquals($expected, $result);
    }
}