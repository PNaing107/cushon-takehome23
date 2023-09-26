<?php

declare(strict_types=1);

namespace App\DataAccess\DAO;

interface DataAccessor
{
    public function getAll(mixed $identifier): array;

    public function getOne(): array;

    public function getModelBinding(): string;
}