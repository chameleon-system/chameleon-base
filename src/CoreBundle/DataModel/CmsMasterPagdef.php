<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class CmsMasterPagdef
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $moduleList;
    /**
     * @var string
     */
    private $layoutFile;

    public function __construct(string $id, array $moduleList, string $layoutFile)
    {
        $this->id = $id;
        $this->moduleList = $moduleList;
        $this->layoutFile = $layoutFile;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getModuleList(): array
    {
        return $this->moduleList;
    }

    public function getLayoutFile(): string
    {
        return $this->layoutFile;
    }


}