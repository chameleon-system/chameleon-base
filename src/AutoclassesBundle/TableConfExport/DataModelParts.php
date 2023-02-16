<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class DataModelParts
{
    public function __construct(private readonly string $property, private readonly string $methods, private readonly bool $defaultValue = false)
    {
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
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->defaultValue;
    }



}