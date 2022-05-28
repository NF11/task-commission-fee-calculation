<?php

namespace App\Models;

class Transaction
{
    public const USER_ID = 'user_id';
    public const CLIENT_TYPE = 'client_type';
    public const OPERATION_TYPE = 'operation_type';
    public const DATE = 'date';
    public const CURRENCY_CODE = 'currency_code';
    public const AMOUNT = 'amount';
    public const INDEX = 'index';

    private ?string $userId = null;

    private ?string $clientType;

    private ?string $operationType;

    private ?string $date;

    private ?string $currencyCode;

    private ?string $amount;

    private string $index;

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getClientType(): ?string
    {
        return $this->clientType;
    }

    public function setClientType(?string $clientType): self
    {
        $this->clientType = $clientType;
        return $this;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function setOperationType(?string $operationType): self
    {
        $this->operationType = $operationType;
        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(?string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): Transaction
    {
        $this->index = $index;
        return $this;
    }

    public function toArray(): array
    {
        return [
            self::USER_ID => $this->userId,
            self::CLIENT_TYPE => $this->clientType,
            self::OPERATION_TYPE => $this->operationType,
            self::DATE => $this->date,
            self::CURRENCY_CODE => $this->currencyCode,
            self::AMOUNT => $this->amount,
            self::INDEX => $this->index
        ];
    }


}
