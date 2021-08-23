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
    private $fallbackLoader;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    public function __construct(DataAccessCmsMasterPagedefInterface $fallbackLoader, InputFilterUtilInterface $inputFilterUtil)
    {
        $this->fallbackLoader = $fallbackLoader;
        $this->inputFilterUtil = $inputFilterUtil;
    }

    public function get(string $id, ?string $type = null): ?CmsMasterPagdef
    {
        //check if the pagedef exists in the database... if it does, use it. if not, use the file
        $oPageDefinitionFile = null;

        // TODO querying the request is quite unfortunate (unexpected) here.
        $requestMasterPageDef = 'true' === $this->inputFilterUtil->getFilteredInput('__masterPageDef');

        if (true === $requestMasterPageDef && true ===  TGlobal::CMSUserDefined()) {
            // load master pagedef...
            $oPageDefinitionFile = TdbCmsMasterPagedef::GetNewInstance();
            $oPageDefinitionFile->Load($this->inputFilterUtil->getFilteredInput('id'));
        } else {
            $oPageDefinitionFile = new TCMSPagedef($id);

            if (null === $oPageDefinitionFile->iMasterPageDefId || empty($oPageDefinitionFile->iMasterPageDefId)) {
                $oPageDefinitionFile->sqlData = false;
            }
        }

        if (false === $oPageDefinitionFile->sqlData) {
            return $this->fallbackLoader->get($id, $type);
        }

        return new CmsMasterPagdef(
            $id,
            $oPageDefinitionFile->GetModuleList(),
            $oPageDefinitionFile->GetLayoutFile()
        );
    }
}
