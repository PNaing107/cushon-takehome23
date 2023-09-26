<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class InvestmentTransaction implements JsonSerializable
{
    private int $id;
    private float $amount;
    private string $symbol;
    private string $name;
    private float $net_asset_value;
    private string $created_at;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNetAssetValue(): float
    {
        return $this->net_asset_value;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'amount' => $this->getAmount(),
            'symbol' => $this->getSymbol(),
            'name' => $this->getName(),
            'net_asset_value' => $this->getAmount(),
            'created_at' => $this->getCreatedAt()
        ];
    }
}
