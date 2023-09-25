<?php

namespace App\Services;

use App\DataAccess\DAO\DataAccessor;

abstract class AbstractService
{
    protected array $responseBody = [
        'message' => 'Something went wrong.',
        'status' => 500,
        'data' => []
    ];
    
    protected DataAccessor $accessor;

    public function __construct(DataAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    public function getAll(): array
    {
        try {
            $data = $this->accessor->getAll();
        } catch (\PDOException $exception) {
            $this->responseBody['message'] = $exception->getMessage();
        }

        if (isset($data) && $data) {

            $model = $this->getModelName($this->accessor->getModelBinding());

            $this->responseBody = [
                'message' => "{$model}s successfully retrieved.",
                'status' => 200,
                'data' => $data
            ];
        }

        return $this->responseBody;
    }

    // Helper methods 
    protected function getModelName(string $fullClassName)
    {
        return (new \ReflectionClass($fullClassName))->getShortName();
    }
}