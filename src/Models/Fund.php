<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class Fund implements JsonSerializable
{
    private int $id;
    private string $symbol;
    private string $name;
    private float $net_asset_value;
    private int $risk_factor;
    private float $ongoing_charge;
    private string $created_at;
    private string $updated_at;
    private ?string $deleted_at;

    public function getId(): int
    {
        return $this->id;
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

    public function getRiskFactor(): int
    {
        return $this->risk_factor;
    }

    public function getOngoingCharge(): float
    {
        return $this->ongoing_charge;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deleted_at;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'symbol' => $this->getSymbol(),
            'name' => $this->getName(),
            'net_asset_value' => $this->getNetAssetValue(),
            'risk_factor' => $this->getRiskFactor(),
            'ongoing_charge' => $this->getOngoingCharge(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}
