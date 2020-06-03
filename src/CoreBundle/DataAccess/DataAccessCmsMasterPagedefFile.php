<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use TCMSPageDefinitionFile;

class DataAccessCmsMasterPagedefFile implements DataAccessCmsMasterPagedefInterface
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var \TGlobal
     */
    private $global;

    public function __construct(InputFilterUtilInterface $inputFilterUtil, \TGlobal $global)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->global = $global;
    }

    public function get(string $id, ?string $type = null): ?CmsMasterPagdef
    {
        $oPageDefinitionFile = new TCMSPageDefinitionFile();
        $fullPageDefPath = $this->getPageDefinitionFilePath($id, $type);
        $pagePath = substr($fullPageDefPath, 0, -strlen($id.'.pagedef.php'));

        if (false === $oPageDefinitionFile->Load($id, $pagePath)) {
            return null;
        }

        return new CmsMasterPagdef(
            $id,
            $oPageDefinitionFile->GetModuleList(),
            $oPageDefinitionFile->GetLayoutFile(),
            $oPageDefinitionFile->allowedRights
        );
    }

    /**
     * returns the full path to a page definition file given the page definition name.
     *
     * @param string $pagedef - name of the pagedef
     *
     * @return string
     */
    private function getPageDefinitionFilePath(string $pagedef, ?string $type)
    {
        if (null === $type) {
            // TODO querying the request is quite unfortunate (unexpected) here. Thus the whole "type" business here is.

            // we can select a location using a get parameter (_pagedefType). it may be one of: Core, Custom-Core, and Customer
            if (null === $type = $this->inputFilterUtil->getFilteredInput('_pagedefType')) {
                $type = 'Core';
            }
        }
        $path = $this->global->_GetPagedefRootPath($type);

        return $path.'/'.$pagedef.'.pagedef.php';
    }
}
