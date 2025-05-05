<?php

namespace ChameleonSystem\AutoclassesBundle\TableConfExport;

class MappingDataModel
{
    public function __construct(readonly string $mappingXml, readonly array $liveCycle)
    {
    }
}
