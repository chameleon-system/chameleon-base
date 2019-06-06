<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use TCMSPagedef;
use TdbCmsMasterPagedef;
use TGlobal;

class DataAccessCmsMasterPagedefDatabase implements DataAccessCmsMasterPagedefInterface
{
    /**
     * @var DataAccessCmsMasterPagedefInterface
     */
    private $fileBasedLoader;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(DataAccessCmsMasterPagedefInterface $fileBasedLoader, InputFilterUtilInterface $inputFilterUtil)
    {
        $this->fileBasedLoader = $fileBasedLoader;
        $this->inputFilterUtil = $inputFilterUtil;
    }

    public function getPagedefObject(string $pagedef): ?CmsMasterPagdef
    {
        //check if the pagedef exists in the database... if it does, use it. if not, use the file
        $oPageDefinitionFile = null;

        $requestMasterPageDef = $this->inputFilterUtil->getFilteredInput('__masterPageDef', false);

        if ($requestMasterPageDef && TGlobal::CMSUserDefined()) {
            // load master pagedef...
            $oPageDefinitionFile = TdbCmsMasterPagedef::GetNewInstance();
            $oPageDefinitionFile->Load($this->inputFilterUtil->getFilteredInput('id'));
        } else {
            $oPageDefinitionFile = new TCMSPagedef($pagedef);

            if (null === $oPageDefinitionFile->iMasterPageDefId || empty($oPageDefinitionFile->iMasterPageDefId)) {
                $oPageDefinitionFile->sqlData = false;
            }
        }

        if (false === $oPageDefinitionFile->sqlData) {
            return $this->fileBasedLoader->getPagedefObject($pagedef);
        }

        return new CmsMasterPagdef(
            $pagedef,
            $oPageDefinitionFile->GetModuleList(),
            $oPageDefinitionFile->GetLayoutFile()
        );
    }
}