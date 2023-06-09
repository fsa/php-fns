<?php

namespace FSA\FNS;

class CheckItemProductCodeData
{
    private int $gtin;
    private string $rawProductCode;
    private int $productIdType;
    private string $sernum;

    private array $undefined = [];

    public function __construct(object $productCodeData)
    {
        foreach (['gtin', 'rawProductCode', 'productIdType', 'sernum'] as $key) {
            if (!isset($productCodeData->$key)) {
                throw new CheckFormatException("ProductCodeData format error.");
            }
        }
        foreach ($productCodeData as $key => $value) {
            switch ($key) {
                case 'gtin':
                case 'rawProductCode':
                case 'productIdType':
                case 'sernum':
                    $this->$key = $value;
                    break;
                default:
                    $this->undefined[$key] = $value;
            }
        }
    }

    public function get(): object
    {
        $values = get_object_vars($this);
        unset($values['undefined']);
        return (object)$values;
    }
}