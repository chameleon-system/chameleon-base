<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\MediaManager\Mapper;

use AbstractViewMapper;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ImageCropBundle\Bridge\Chameleon\BackendModule\ImageCropEditorModule;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;

class ImageCropEditorUrlMapper extends AbstractViewMapper
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var AuthenticityTokenManagerInterface
     */
    private $authenticityTokenManager;

    public function __construct(
        UrlUtil $urlUtil,
        AuthenticityTokenManagerInterface $authenticityTokenManager
    ) {
        $this->urlUtil = $urlUtil;
        $this->authenticityTokenManager = $authenticityTokenManager;
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('mediaItem', MediaItemDataModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        /**
         * @var $mediaItem MediaItemDataModel
         */
        $mediaItem = $oVisitor->GetSourceObject('mediaItem');
        $oVisitor->SetMappedValue('createCropUrl', $this->getCropEditorUrl($mediaItem->getId()));
        $oVisitor->SetMappedValue('deleteCropUrl', $this->getDeleteCropUrl());
    }

    /**
     * @param string $mediaItemId
     *
     * @return string
     */
    private function getCropEditorUrl($mediaItemId)
    {
        $parameters = array(
            'pagedef' => ImageCropEditorModule::PAGEDEF_NAME,
            '_pagedefType' => ImageCropEditorModule::PAGEDEF_TYPE,
            ImageCropEditorModule::URL_PARAM_IMAGE_ID => $mediaItemId,
        );

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    private function getDeleteCropUrl(): string
    {
        $parameters = array(
            'pagedef' => ImageCropEditorModule::PAGEDEF_NAME,
            '_pagedefType' => ImageCropEditorModule::PAGEDEF_TYPE,
            'module_fnc' => array('contentmodule' => 'deleteCrop'),
        );

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }
}
