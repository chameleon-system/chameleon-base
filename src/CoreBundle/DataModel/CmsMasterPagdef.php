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

    /**
     * @var \TdbCmsRight[]
     */
    private $allowedRights;

    /**
     * @param string         $id
     * @param array          $moduleList
     * @param string         $layoutFile
     * @param \TdbCmsRight[] $allowedRights
     */
    public function __construct(string $id, array $moduleList, string $layoutFile, array $allowedRights)
    {
        $this->id = $id;
        $this->moduleList = $moduleList;
        $this->layoutFile = $layoutFile;
        $this->allowedRights = $allowedRights;
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

    /**
     * @return \TdbCmsRight[]
     */
    public function getAllowedRights(): array
    {
        return $this->allowedRights;
    }
}
