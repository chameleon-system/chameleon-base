<?php

namespace ChameleonSystem\ImageEditorBundle\Service;

use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ImageCropBundle\Bridge\Chameleon\BackendModule\ImageCropEditorModule;
use ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\BackendModule\ImageEditorModule;
use ChameleonSystem\ImageEditorBundle\Interface\ImageEditorUrlServiceInterface;

class ImageEditorUrlService implements ImageEditorUrlServiceInterface
{
    public function __construct(
        private readonly UrlUtil $urlUtil,
    ) {
    }

    public function getImageEditorUrl(string $mediaItemId): string
    {
        $parameters = [
            'pagedef' => ImageEditorModule::PAGEDEF_NAME,
            '_pagedefType' => ImageEditorModule::PAGEDEF_TYPE,
            ImageCropEditorModule::URL_PARAM_IMAGE_ID => $mediaItemId,
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }
}
