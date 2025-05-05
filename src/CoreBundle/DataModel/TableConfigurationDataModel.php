<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class TableConfigurationDataModel
{
    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public ?string $cmsUsergroupId
    ) {
    }
}
