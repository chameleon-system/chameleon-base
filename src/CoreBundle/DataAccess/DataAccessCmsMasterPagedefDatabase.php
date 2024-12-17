<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

class DataAccessCmsMasterPagedefDatabase implements DataAccessCmsMasterPagedefInterface
{
    public function __construct(
        private readonly DataAccessCmsMasterPagedefInterface $fallbackLoader,
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly SecurityHelperAccess $securityHelperAccess)
    {
    }

    public function get(string $id): ?CmsMasterPagdef
    {
        // check if the pagedef exists in the database... if it does, use it. if not, use the file
        $requestMasterPageDef = 'true' === $this->inputFilterUtil->getFilteredInput('__masterPageDef');

        if (true === $requestMasterPageDef && true === $this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            // load master pagedef...
            $oPageDefinitionFile = \TdbCmsMasterPagedef::GetNewInstance();
            $oPageDefinitionFile->Load($this->inputFilterUtil->getFilteredInput('id'));
        } else {
            $oPageDefinitionFile = new \TCMSPagedef($id);

            if (empty($oPageDefinitionFile->iMasterPageDefId)) {
                $oPageDefinitionFile->sqlData = false;
            }
        }

        if (false === $oPageDefinitionFile->sqlData) {
            return $this->fallbackLoader->get($id);
        }

        return new CmsMasterPagdef(
            $id,
            $oPageDefinitionFile->GetModuleList(),
            $oPageDefinitionFile->GetLayoutFile()
        );
    }
}
