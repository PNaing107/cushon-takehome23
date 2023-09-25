<?php

namespace App\Services;

use App\DataAccess\Database;
use App\DataAccess\FundDAO;
use App\Models\CoursesModel;

class FundService extends AbstractService
{
    protected FundDAO $fundDAO;

    protected array $responseBody = [
        'message' => 'Something went wrong.',
        'status' => 500,
        'data' => []
    ];

    public function __construct(FundDAO $fundDAO)
    {
        $this->fundDAO = $fundDAO;
    }

    public function getFunds(): array
    {
        try {
            $funds = $this->fundDAO->getAll();
        } catch (\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
        }

        if (isset($funds) && $funds) {

            $this->responseBody = [
                'message' => 'Funds successfully retrieved.',
                'status' => 200,
                'data' => $funds
            ];
        }

        return $this->responseBody;
    }
}