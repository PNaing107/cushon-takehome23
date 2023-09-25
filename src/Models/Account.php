<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class Account implements JsonSerializable
{
    private string $id;
    private string $customer_id;
    private string $account_type_name;
    private string $created_at;
    private string $deleted_at;

    public function getId(): string
    {
        return $this->id;
    }

    public function getCustomerId(): string
    {
        return $this->customer_id;
    }

    public function getAccountTypeName(): string
    {
        return $this->account_type_name;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getDeletedAt(): string
    {
        return $this->deleted_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getID(),
            'customer_id' => $this->getCustomerId(),
            'account_type' => $this->getAccountTypeName(),
            'created_at' => $this->getCreatedAt()
        ];
    }

}