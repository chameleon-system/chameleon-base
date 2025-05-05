<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererSnippetGallery_To_List_Mapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('aSnippetList', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $aList = [];
        /**
         * @var TPkgViewRendererSnippetGalleryItem[] $aSnippetList
         */
        $aSnippetList = $oVisitor->GetSourceObject('aSnippetList');
        foreach ($aSnippetList as $sSnippetName => $oSnippetData) {
            try {
                $aList[] = [
                    'sName' => $sSnippetName,
                    'sContent' => $this->renderListItem($oSnippetData),
                ];
            } catch (TPkgSnippetRenderer_SnippetRenderingException $e) {
                throw new MapperException(sprintf('Error while rendering: %s', $e->getMessage()), $e->getCode(), $e);
            }
        }

        $oVisitor->SetMappedValue('aList', $aList);
    }

    /**
     * @return string
     *
     * @throws MapperException
     * @throws TPkgSnippetRenderer_SnippetRenderingException
     */
    protected function renderListItem(TPkgViewRendererSnippetGalleryItem $oSnippetData)
    {
        $oViewRenderer = new ViewRenderer();
        $aDummyData = $oSnippetData->getDummyData();
        if (true === is_array($aDummyData)) {
            $oViewRenderer->AddSourceObjectsFromArray($aDummyData);
        }

        return $oViewRenderer->Render($oSnippetData->sRelativePath.'/'.$oSnippetData->sSnippetName);
    }
}
