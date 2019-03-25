<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

class MediaManagerUrlGenerator implements MediaManagerUrlGeneratorInterface
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @param UrlUtil $urlUtil
     */
    public function __construct(UrlUtil $urlUtil)
    {
        $this->urlUtil = $urlUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getStandaloneMediaManagerUrl()
    {
        $parameters = array(
            'pagedef' => 'CMSMediaManager',
        );

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * This version only supports opening the media manager in a new window, install MediaManagerBundle to enable this
     * option.
     *
     * {@inheritdoc}
     */
    public function openStandaloneMediaManagerInNewWindow()
    {
        return true;
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
        $parameters = array(
            'pagedef' => 'CMSImageManagerLoadImage',
            'imagefieldname' => $imageFieldName,
            'tableid' => $tableId,
            'id' => $recordId,
            'position' => $position,
        );

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlToPickImageForWysiwyg($javaScriptCallbackFunctionName = 'selectImage')
    {
        $parameters = array(
            'pagedef' => 'CMSwysiwygImageChooser',
            'sAllowedFileTypes' => 'jpg,png,gif,jpeg',
        );

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }
}
