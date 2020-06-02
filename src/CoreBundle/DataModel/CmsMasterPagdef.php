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
     * @var array|\TdbCmsRole[]
     */
    private $allowedRoles;

    /**
     * @param string        $id
     * @param array         $moduleList
     * @param string        $layoutFile
     * @param \TdbCmsRole[] $allowedRoles
     */
    public function __construct(string $id, array $moduleList, string $layoutFile, array $allowedRoles)
    {
        $this->id = $id;
        $this->moduleList = $moduleList;
        $this->layoutFile = $layoutFile;
        $this->allowedRoles = $allowedRoles;
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

    public function getAllowedRoles()
    {
        return $this->allowedRoles;
    }
}
