<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererSnippetDummyData
{
    /**
     * @var array<string, string|array>
     */
    private $aData = [];

    /**
     * @param string $sKey
     * @param string|array $sVal
     *
     * @return void
     */
    public function addDummyData($sKey, $sVal)
    {
        $this->aData[$sKey] = $sVal;
    }

    /**
     * @param array<string, string|array> $aDummyData
     *
     * @return void
     */
    public function addDummyDataArray($aDummyData)
    {
        foreach (array_keys($aDummyData) as $sKey) {
            $this->addDummyData($sKey, $aDummyData[$sKey]);
        }
    }

    /**
     * @return array
     */
    public function getDummyData()
    {
        return $this->aData;
    }

    /**
     * @param bool $bSuppressWarnings
     * @param string $sPathRelativeToSnippetsFolder
     *
     * @return void
     *
     * @throws ErrorException
     */
    public function addDummyDataFromFile($sPathRelativeToSnippetsFolder, $bSuppressWarnings = false, ?TdbCmsPortal $oPortal = null)
    {
        $oDummyData = null;

        $sFilePath = $this->getDummyDataFilePath($sPathRelativeToSnippetsFolder, $oPortal);
        if ('' !== $sFilePath) {
            $oDummyData = $this->loadDummyFile($sFilePath);
        }

        $aTypeList = $this->getViewRendererSnippetDirectory()->getBasePaths($oPortal);

        if (null === $oDummyData) {
            if (false === $bSuppressWarnings) {
                throw new ErrorException("unable to find dummy data file '{$sPathRelativeToSnippetsFolder}' in ".print_r($aTypeList, true), 0, E_USER_WARNING, __FILE__, __LINE__);
            }
        } else {
            $aDummyData = [];
            if ($oDummyData instanceof self) {
                /** @var TPkgViewRendererSnippetDummyData $oDummyData */
                $aDummyData = $oDummyData->getDummyData();
            } else {
                $aDummyData = $oDummyData;
            }
            if (is_array($aDummyData)) {
                $this->addDummyDataArray($aDummyData);
            } else {
                if (false === $bSuppressWarnings) {
                    throw new ErrorException("data included from '{$sPathRelativeToSnippetsFolder}' is invalid.", 0, E_USER_WARNING, __FILE__, __LINE__);
                }
            }
        }
    }

    /**
     * checks all snippet paths based on portal themes to locate the dummy file.
     *
     * @param string $sPathRelativeToSnippetsFolder
     *
     * @return string
     */
    public function getDummyDataFilePath($sPathRelativeToSnippetsFolder, ?TdbCmsPortal $oPortal = null)
    {
        static $aDummyFilePaths = null;

        $sCacheKey = $sPathRelativeToSnippetsFolder;
        if (null !== $oPortal) {
            $sCacheKey .= $oPortal->id;
        }

        if (null !== $aDummyFilePaths && isset($aDummyFilePaths[$sCacheKey])) {
            return $aDummyFilePaths[$sCacheKey];
        }

        $aTypeList = $this->getViewRendererSnippetDirectory()->getBasePaths($oPortal);
        $sRootPath = '';

        $oDummyData = null;
        foreach ($aTypeList as $sType) {
            $sFile = $sType.'/'.$sRootPath.'/'.$sPathRelativeToSnippetsFolder;
            if (file_exists($sFile)) {
                $aDummyFilePaths[$sCacheKey] = $sFile;

                return $sFile;
            }
        }

        return '';
    }

    /**
     * return a dummy image URL that can be used in twig with the filter cmsthumb().
     *
     * @param string $sImageText
     *
     * @return string
     */
    public function getDummyImage($sImageText = '')
    {
        $sURL = '//placehold.it/[{width}]x[{height}]';
        if (!empty($sImageText)) {
            $sURL .= '&'.TTools::GetArrayAsURL(['text' => $sImageText]);
        }

        return $sURL;
    }

    /**
     * include data from another dummy file under an alias.
     *
     * @param string $sAliasName
     * @param string $sPathRelativeToSnippetsFolder
     * @param bool $bSuppressWarnings
     *
     * @return void
     */
    public function addDummyDataFromFileAs($sAliasName, $sPathRelativeToSnippetsFolder, $bSuppressWarnings = false)
    {
        $oDummy = new self();
        $oDummy->addDummyDataFromFile($sPathRelativeToSnippetsFolder, $bSuppressWarnings);
        $aData = $oDummy->getDummyData();
        $this->addDummyData($sAliasName, $aData);
    }

    /**
     * @param string $sFile
     */
    private function loadDummyFile($sFile)
    {
        return include $sFile;
    }

    /**
     * @param string $sKey
     *
     * @return void
     */
    public function removeData($sKey)
    {
        if (isset($this->aData[$sKey])) {
            unset($this->aData[$sKey]);
        }
    }

    /**
     * render a snippet with dummy data.
     *
     * @param string $sSnippetPath - path relative to the snippet folder
     *
     * @return string
     */
    public function renderSnippet($sSnippetPath)
    {
        $sDummyFile = substr($sSnippetPath, 0, -9).'dummy.php';
        $oDummyData = new self();
        $oDummyData->addDummyDataFromFile($sDummyFile);
        $oView = new ViewRenderer();
        $oView->AddSourceObjectsFromArray($oDummyData->getDummyData());

        return $oView->Render($sSnippetPath);
    }

    /**
     * @return TPkgViewRendererSnippetDirectoryInterface
     */
    private function getViewRendererSnippetDirectory()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }
}
