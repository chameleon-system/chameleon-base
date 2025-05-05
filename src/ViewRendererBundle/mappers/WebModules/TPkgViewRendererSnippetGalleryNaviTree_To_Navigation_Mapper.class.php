<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererSnippetGalleryNaviTree_To_Navigation_Mapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('aTree', 'array');
        $oRequirements->NeedsSourceObject('sActiveRelativePath', 'string', '');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var array $aTree */
        $aTree = $oVisitor->GetSourceObject('aTree');

        $oVisitor->SetMappedValue('aTree', $this->MapTree($aTree, '', $oVisitor->GetSourceObject('sActiveRelativePath')));
    }

    /**
     * @param array $aTree
     * @param string $sCurrentPath
     * @param string $sActiveRelativePath
     *
     * @return array
     */
    protected function MapTree($aTree, $sCurrentPath, $sActiveRelativePath)
    {
        $aNewTree = [];
        foreach ($aTree as $sDirectory => $varContent) {
            $sNewCurrentPath = $sCurrentPath;
            if (false === empty($sNewCurrentPath)) {
                $sNewCurrentPath .= '/';
            }
            $sNewCurrentPath = $sNewCurrentPath.$sDirectory;
            $aNewTreeItem = [
                'sTitle' => $sDirectory,
                'bIsActive' => ($sNewCurrentPath == $sActiveRelativePath),
                'bIsExpanded' => (substr($sActiveRelativePath, 0, strlen($sNewCurrentPath)) === $sNewCurrentPath),
                'sLink' => '?'.TTools::GetArrayAsURL(['sActiveRelativePath' => $sNewCurrentPath]),
                'aChildren' => [],
            ];
            if (is_array($varContent)) {
                $aNewTreeItem['aChildren'] = $this->MapTree($varContent, $sNewCurrentPath, $sActiveRelativePath);
            }
            $aNewTree[] = $aNewTreeItem;
        }

        return $aNewTree;
    }
}
