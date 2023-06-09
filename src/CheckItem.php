<?php

namespace FSA\FNS;

class CheckItem
{
    private string $name;
    private int $nds;
    private int $paymentType;
    private int $price;
    private float $quantity;
    private int $sum;

    private ?int $ndsSum = null;
    private ?CheckItemProductCodeData $productCodeData = null;

    // Нашлось на некоторых продуктах
    private int $productType;
    private string $productCodeDataError;

    private array $undefined = [];

    public function __construct(private string $_id, object $item)
    {
        foreach (['name', 'nds', 'paymentType', 'price', 'quantity', 'sum'] as $key) {
            if (!isset($item->$key)) {
                throw new CheckFormatException("Item format error: {$item->name}");
            }
        }
        foreach ($item as $key => $value) {
            switch ($key) {
                case 'name':
                case 'nds':
                case 'paymentType':
                case 'price':
                case 'quantity':
                case 'sum':
                case 'productType':
                case 'productCodeDataError':
                case 'ndsSum':
                    $this->$key = $value;
                    break;
                case 'productCodeData':
                    $this->$key = new CheckItemProductCodeData($value);
                    break;
                default:
                    $this->undefined[$key] = $value;
            }
        }
    }

    public function getId(): string
    {
        return $this->_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getSum(): int
    {
        return $this->sum;
    }

    public function getNdsSum(): ?int
    {
        return $this->ndsSum;
    }

    public function getProductCodeData(): ?CheckItemProductCodeData
    {
        return $this->productCodeData;
    }

    public function get(): object
    {
        $values = get_object_vars($this);
        unset($values['_id']);
        unset($values['undefined']);
        return (object)$values;
    }

    public function getOther(): ?object
    {
        return count($this->undefined)?(object)($this->undefined):null;
    }
}
