<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

class TPkgViewRendererMapper_ListHandler extends AbstractViewMapper
{
    public const SOURCE_DATA_NAME = '__cmsListHandlerData';
    public const SOURCE_DATA_INPUT = '__cmsListHandlerInput';
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject(self::SOURCE_DATA_NAME, 'array'); // of TPkgViewRendererMapper_ListHandlerData
        $oRequirements->NeedsSourceObject(self::SOURCE_DATA_INPUT, 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /**
         * @var TPkgViewRendererMapper_ListHandlerData[] $aListHandlerData
         */
        $aListHandlerData = $oVisitor->GetSourceObject(self::SOURCE_DATA_NAME);
        $aListHandlerInput = $oVisitor->GetSourceObject(self::SOURCE_DATA_INPUT);
        if (isset($aListHandlerInput[self::SOURCE_DATA_NAME])) {
            unset($aListHandlerInput[self::SOURCE_DATA_NAME]);
        }
        foreach ($aListHandlerData as $oListHandlerData) {
            /** @var TCMSRecordList $oSourceList */
            $oSourceList = $aListHandlerInput[$oListHandlerData->getSourceVariableName()];
            $aTargetData = [];
            $oSourceList->GoToStart();
            while ($oSource = $oSourceList->Next()) {
                /**
                 * @var ViewRenderer $oViewRenderer
                 */
                $oViewRenderer = $this->container->get('chameleon_system_view_renderer.view_renderer');
                foreach ($oListHandlerData->getMapperChain() as $sMapper) {
                    try {
                        $oViewRenderer->addMapperFromIdentifier($sMapper);
                    } catch (LogicException $e) {
                        throw new MapperException(sprintf('Invalid mapper: %s', $sMapper), $e->getCode(), $e);
                    }
                }
                $oViewRenderer->AddSourceObjectsFromArray($aListHandlerInput);
                $oViewRenderer->AddSourceObject($oListHandlerData->getItemName(), $oSource);
                try {
                    $aTargetData[] = $oViewRenderer->Render($oListHandlerData->getSnippetName());
                } catch (TPkgSnippetRenderer_SnippetRenderingException $e) {
                    throw new MapperException(sprintf('Error while rendering: %s', $e->getMessage()), $e->getCode(), $e);
                }
                $cacheTrigger = $oViewRenderer->getPostRenderMapperCacheTrigger();
                if (null === $cacheTrigger || 0 === count($cacheTrigger)) {
                    continue;
                }
                foreach ($cacheTrigger as $entry) {
                    $oCacheTriggerManager->addTrigger($entry['table'], $entry['id']);
                }
            }
            $oVisitor->SetMappedValue($oListHandlerData->getTargetVariableName(), $aTargetData);
        }
    }
}
