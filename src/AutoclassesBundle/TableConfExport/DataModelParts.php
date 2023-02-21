<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class DataModelParts
{
    public function __construct(
        private readonly string $property,
        private readonly string $methods,
        private readonly array $includes = [],
        private readonly bool $defaultValue = false
    ) {
    }

    public function merge(DataModelParts $additional): DataModelParts
    {
        return new DataModelParts(
            implode(",\n", [$this->property, $additional->property]),
            implode("\n", [$this->methods . $additional->methods]),
            array_merge($this->includes, $additional->includes),
            $this->defaultValue || $additional->defaultValue
        );
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getMethods(): string
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }



    /**
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->defaultValue;
    }


}