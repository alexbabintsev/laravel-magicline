<?php

namespace alexbabintsev\Magicline\DataTransferObjects;

use ReflectionClass;
use ReflectionProperty;

abstract class BaseDto
{
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    public static function from(array $data): static
    {
        return new static($data); // @phpstan-ignore new.static
    }

    public static function collection(array $items): array
    {
        return array_map(fn ($item) => static::from($item), $items);
    }

    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if ($value instanceof self) {
                $result[$property->getName()] = $value->toArray();
            } elseif (is_array($value) && ! empty($value) && $value[0] instanceof self) {
                $result[$property->getName()] = array_map(fn ($item) => $item->toArray(), $value);
            } else {
                $result[$property->getName()] = $value;
            }
        }

        return $result;
    }

    private function fill(array $data): void
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            if (array_key_exists($propertyName, $data)) {
                $this->{$propertyName} = $data[$propertyName];
            }
        }
    }
}
