<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class TableConfigurationDataModel
{
    public function __construct(
        readonly private string $id,
        readonly private string $name,
        readonly private string $cmsUsergroupId
    ) {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCmsUsergroupId(): string
    {
        return $this->cmsUsergroupId;
    }
}