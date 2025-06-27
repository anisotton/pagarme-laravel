<?php

declare(strict_types=1);

namespace Anisotton\Pagarme\DataTransferObjects;

abstract class BaseDto
{
    public function toArray(): array
    {
        $array = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $value = $property->getValue($this);

            if ($value instanceof BaseDto) {
                $array[$property->getName()] = $value->toArray();
            } elseif (is_array($value)) {
                $array[$property->getName()] = $this->arrayToArray($value);
            } elseif ($value !== null) {
                $array[$property->getName()] = $value;
            }
        }

        return $array;
    }

    private function arrayToArray(array $array): array
    {
        return array_map(function ($item) {
            if ($item instanceof BaseDto) {
                return $item->toArray();
            }
            return $item;
        }, $array);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}
