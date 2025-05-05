<?php

namespace ChameleonSystem\CoreBundle\DataAccess;

use ChameleonSystem\CoreBundle\DataModel\CmsMasterPagdef;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

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

    public function get(string $id): ?CmsMasterPagdef
    {
        $oPageDefinitionFile = new \TCMSPageDefinitionFile();
        $fullPageDefPath = $this->PageDefinitionFile($id);
        $pagePath = substr($fullPageDefPath, 0, -strlen($id.'.pagedef.php'));

        if (false === $oPageDefinitionFile->Load($id, $pagePath)) {
            return null;
        }

        return new CmsMasterPagdef(
            $id,
            $oPageDefinitionFile->GetModuleList(),
            $oPageDefinitionFile->GetLayoutFile()
        );
    }

    /**
     * returns the full path to a page definition file given the page definition name.
     *
     * @param string $pagedef - name of the pagedef
     *
     * @return string
     */
    private function PageDefinitionFile(string $pagedef)
    {
        // we can select a location using a get parameter (_pagedefType). it may be one of: Core, Custom-Core, and Customer
        if (null === $pagedefType = $this->inputFilterUtil->getFilteredInput('_pagedefType')) {
            $pagedefType = 'Core';
        }
        $path = $this->global->_GetPagedefRootPath($pagedefType);

        return $path.'/'.$pagedef.'.pagedef.php';
    }
}
