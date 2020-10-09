<?php

namespace ChameleonSystem\CoreBundle\DataModel;

class CmsMasterPagdefFile extends CmsMasterPagdef
{
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
        parent::__construct($id, $moduleList, $layoutFile);

        $this->allowedRights = $allowedRights;
    }

    /**
     * @return \TdbCmsRight[]
     */
    public function getAllowedRights(): array
    {
        return $this->allowedRights;
    }
}
