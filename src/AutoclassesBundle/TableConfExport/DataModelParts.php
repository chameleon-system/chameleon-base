<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class DataModelParts
{
    public function __construct(
        private readonly string $property,
        private readonly string $methods,
        private readonly string $mappingXml,
        private readonly array $classImports = [],
        private readonly bool $defaultValue = false,
        /** @var array<string, string> - key is the event, value the method */
        private readonly array $liveCycleCallbacks = []
    ) {
    }

    public function merge(DataModelParts $additional): DataModelParts
    {
        return new DataModelParts(
            implode(",\n", [$this->property, $additional->property]),
            implode("\n", [$this->methods.$additional->methods]),
            sprintf("%s\n%s", $this->mappingXml, $additional->mappingXml),
            array_merge($this->classImports, $additional->classImports),
            $this->defaultValue || $additional->defaultValue,
            array_merge($this->liveCycleCallbacks, $additional->liveCycleCallbacks)
        );
    }

    /**
     * @return string
     */
    public function getMappingXml(): string
    {
        return $this->mappingXml;
    }

    /**
     * @return array
     */
    public function getLiveCycleCallbacks(): array
    {
        return $this->liveCycleCallbacks;
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
    public function getClassImports(): array
    {
        return $this->classImports;
    }



    /**
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->defaultValue;
    }


}