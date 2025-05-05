<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon;

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\MediaManager\MediaManagerListState;
use ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\BackendModule\MediaManagerBackendModule;

class MediaManagerUrlGenerator implements MediaManagerUrlGeneratorInterface
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var bool
     */
    private $openInNewWindow;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    /**
     * @param bool $openInNewWindow
     */
    public function __construct(
        UrlUtil $urlUtil,
        $openInNewWindow,
        InputFilterUtilInterface $inputFilterUtil
    ) {
        $this->urlUtil = $urlUtil;
        $this->openInNewWindow = $openInNewWindow;
        $this->inputFilterUtil = $inputFilterUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getStandaloneMediaManagerUrl()
    {
        $parameters = [
            'pagedef' => MediaManagerBackendModule::PAGEDEF_NAME,
            '_pagedefType' => MediaManagerBackendModule::PAGEDEF_TYPE,
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * {@inheritdoc}
     */
    public function openStandaloneMediaManagerInNewWindow()
    {
        return $this->openInNewWindow;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlToPickImage(
        $javaScriptCallbackFunctionName = 'parent._SetImage',
        $canUseCrop = false,
        $imageFieldName = null,
        $tableId = null,
        $recordId = null,
        $position = 0
    ) {
        $parameters = [
            'pagedef' => MediaManagerBackendModule::PAGEDEF_NAME_PICK_IMAGE,
            '_pagedefType' => MediaManagerBackendModule::PAGEDEF_TYPE,
            MediaManagerListState::URL_NAME_PICK_IMAGE_MODE => '1',
            MediaManagerListState::URL_NAME_PICK_IMAGE_CALLBACK => $javaScriptCallbackFunctionName,
            MediaManagerListState::URL_NAME_PICK_IMAGE_WITH_CROP => true === $canUseCrop ? '1' : '0',
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlToPickImageForWysiwyg($javaScriptCallbackFunctionName = 'selectImage')
    {
        $parameters = [
            'pagedef' => MediaManagerBackendModule::PAGEDEF_NAME_PICK_IMAGE,
            '_pagedefType' => MediaManagerBackendModule::PAGEDEF_TYPE,
            MediaManagerListState::URL_NAME_PICK_IMAGE_MODE => '1',
            MediaManagerListState::URL_NAME_PICK_IMAGE_CALLBACK => $javaScriptCallbackFunctionName,
            MediaManagerListState::URL_NAME_PICK_IMAGE_WITH_CROP => '0',
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }
}
