<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\BackendModule;

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ImageCrop\DataModel\CmsMediaDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;
use ChameleonSystem\ImageCrop\Exception\ImageCropDataAccessException;
use ChameleonSystem\ImageCrop\Exception\ImageCropEditorException;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\CropImageServiceInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropPresetDataAccessInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageCropEditorModule extends \MTPkgViewRendererAbstractModuleMapper
{
    public const PAGEDEF_NAME = 'imageCropEditor';

    public const PAGEDEF_TYPE = '@ChameleonSystemImageCropBundle';

    public const URL_PARAM_IMAGE_ID = 'cmsMediaId';

    public const URL_PARAM_PRESET_NAME = 'preset';

    public const URL_PARAM_PRESET_RESTRICTION = 'presetRestriction';

    public const URL_PARAM_ENABLE_CALLBACK = 'enableCallback';

    public const URL_PARAM_FIELD_NAME = 'fieldName';

    public const URL_PARAM_SAVED = 'saved';

    public const URL_PARAM_CROP_ID = 'cropId';

    public const URL_PARAM_PARENT_IFRAME = 'parentIFrame';

    public const MESSAGE_CONSUMER_NAME = 'imageCropEditor';

    /**
     * @var ImageCropPresetDataAccessInterface
     */
    private $imageCropPresetDataAccess;

    /**
     * @var CmsMediaDataAccessInterface
     */
    private $cmsMediaDataAccess;

    /**
     * @var ImageCropDataAccessInterface
     */
    private $imageCropDataAccess;

    /**
     * @var CropImageServiceInterface
     */
    private $cropImageService;

    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var \ICmsCoreRedirect
     */
    private $redirectService;

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var FlashMessageServiceInterface
     */
    private $flashMessageService;

    public function __construct(
        ImageCropPresetDataAccessInterface $imageCropPresetDataAccess,
        CmsMediaDataAccessInterface $cmsMediaDataAccess,
        ImageCropDataAccessInterface $imageCropDataAccess,
        CropImageServiceInterface $cropImageService,
        UrlUtil $urlUtil,
        \ICmsCoreRedirect $redirectService,
        InputFilterUtilInterface $inputFilterUtil,
        TranslatorInterface $translator,
        LanguageServiceInterface $languageService,
        FlashMessageServiceInterface $flashMessageService,
        readonly private BackendSessionInterface $backendSession
    ) {
        parent::__construct();
        $this->imageCropPresetDataAccess = $imageCropPresetDataAccess;
        $this->cmsMediaDataAccess = $cmsMediaDataAccess;
        $this->imageCropDataAccess = $imageCropDataAccess;
        $this->cropImageService = $cropImageService;
        $this->urlUtil = $urlUtil;
        $this->redirectService = $redirectService;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->translator = $translator;
        $this->languageService = $languageService;
        $this->flashMessageService = $flashMessageService;
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $cmsImage = $this->getCmsImage();
        $crop = $this->getCrop();
        $preset = $this->getPreset();

        $oVisitor->SetMappedValue('cmsImage', $cmsImage);
        $oVisitor->SetMappedValue('preset', $preset);
        if (null === $crop && null !== $preset && null !== $cmsImage) {
            $crop = $this->imageCropDataAccess->getImageCrop($cmsImage, $preset);
        }
        $oVisitor->SetMappedValue('existingCrop', $crop);

        $aspectRadio = null;
        if (null !== $preset) {
            $aspectRadio = $preset->getWidth() / $preset->getHeight();
        }
        $oVisitor->SetMappedValue('aspectRatio', $aspectRadio);

        $enableCallback = $this->inputFilterUtil->getFilteredInput(
            self::URL_PARAM_ENABLE_CALLBACK,
            false
        ) ? true : false;
        $oVisitor->SetMappedValue('enableCallback', $enableCallback);
        $oVisitor->SetMappedValue(
            'fieldName',
            $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_FIELD_NAME)
        );
        $oVisitor->SetMappedValue('saved', $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_SAVED, false));

        $parameters = $this->getUrlParameters();
        $oVisitor->SetMappedValue('urlParameters', $parameters);
        $oVisitor->SetMappedValue('pagedef', self::PAGEDEF_NAME);

        $oVisitor->SetMappedValue('presetList', $this->getPresetList());
        try {
            $oVisitor->SetMappedValue('customCropList', $this->getCustomCropList($crop));
        } catch (ImageCropEditorException $e) {
            $oVisitor->SetMappedValue(
                'errorMessage',
                $this->translator->trans('chameleon_system_image_crop.editor.crop_list_could_not_be_loaded')
            );
        }

        if ($this->flashMessageService->consumerHasMessages(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER)) {
            $oVisitor->SetMappedValue(
                'renderedTableEditorMessages',
                $this->flashMessageService->renderMessages(
                    \TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                    'standard',
                    'Core'
                )
            );
        }

        $oVisitor->SetMappedValue('urlToGetImage', $this->generateUrlToGetImage($cmsImage));

        $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();

        $breadcrumb->AddItem(
            $parameters,
            $this->translator->trans(
                'chameleon_system_image_crop.editor.edit_crop_window_title',
                ['%cropName%' => $cmsImage->getName()]
            )
        );
    }

    /**
     * @return CmsMediaDataModel|null
     */
    private function getCmsImage()
    {
        $imageId = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_IMAGE_ID);

        return $this->cmsMediaDataAccess->getCmsMedia($imageId, $this->backendSession->getCurrentEditLanguageId());
    }

    /**
     * @return ImageCropDataModel|null
     */
    private function getCrop()
    {
        $cropId = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_CROP_ID);
        if (null === $cropId) {
            return null;
        }

        return $this->imageCropDataAccess->getImageCropById(
            $cropId,
            $this->backendSession->getCurrentEditLanguageId()
        );
    }

    /**
     * @return ImageCropPresetDataModel|null
     */
    private function getPreset()
    {
        $systemName = $this->getPresetSystemName();
        if (null === $systemName) {
            return null;
        }

        return $this->imageCropPresetDataAccess->getPresetBySystemName(
            $systemName,
            $this->backendSession->getCurrentEditLanguageId()
        );
    }

    /**
     * @return string|null
     *
     * @psalm-suppress InvalidReturnStatement - We know that this is string|null here
     */
    private function getPresetSystemName()
    {
        $crop = $this->getCrop();
        if (null !== $crop) {
            $preset = $crop->getImageCropPreset();
            if (null === $preset) {
                return null;
            }

            return $preset->getSystemName();
        }

        if (null === $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_PRESET_NAME)) {
            $presetRestriction = $this->getPresetRestriction();
            if (count($presetRestriction) > 0) {
                return $presetRestriction[0];
            }

            return null;
        }

        return $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_PRESET_NAME);
    }

    /**
     * @return array
     */
    private function getUrlParameters()
    {
        $cmsImage = $this->getCmsImage();
        $parameters = [
            'pagedef' => self::PAGEDEF_NAME,
            '_pagedefType' => self::PAGEDEF_TYPE,
            self::URL_PARAM_IMAGE_ID => null !== $cmsImage ? $cmsImage->getId() : '1',
        ];

        $presetSystemName = $this->getPresetSystemName();
        if (null !== $presetSystemName) {
            $parameters[self::URL_PARAM_PRESET_NAME] = $presetSystemName;
        }

        $enableCallback = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_ENABLE_CALLBACK);
        if (null !== $enableCallback) {
            $parameters[self::URL_PARAM_ENABLE_CALLBACK] = $enableCallback;
        }

        $fieldName = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_FIELD_NAME);
        if (null !== $fieldName) {
            $parameters[self::URL_PARAM_FIELD_NAME] = $fieldName;
        }

        $parentIFrame = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_PARENT_IFRAME);
        if (null !== $parentIFrame) {
            $parameters[self::URL_PARAM_PARENT_IFRAME] = $parentIFrame;
        }

        return $parameters;
    }

    /**
     * @return array
     */
    private function getPresetList()
    {
        $presetRestriction = $this->getPresetRestriction();
        $presetSystemName = $this->getPresetSystemName();
        $parameters = $this->getUrlParameters();

        $presetList = [];
        $presets = \TdbCmsImageCropPresetList::GetList();
        $presetRestrictionCount = \count($presetRestriction);
        while ($presetAvailable = $presets->Next()) {
            if (0 !== $presetRestrictionCount && false === in_array(
                $presetAvailable->fieldSystemName,
                $presetRestriction,
                true
            )) {
                continue;
            }
            $parameters[self::URL_PARAM_PRESET_NAME] = $presetAvailable->fieldSystemName;
            if ($presetRestrictionCount > 0) {
                $parameters[self::URL_PARAM_PRESET_RESTRICTION] = implode(';', $presetRestriction);
            }
            $presetList[] = [
                'name' => $presetAvailable->fieldName,
                'url' => URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&'),
                'active' => $presetAvailable->fieldSystemName === $presetSystemName,
            ];
        }

        return $presetList;
    }

    /**
     * @return array
     */
    private function getPresetRestriction()
    {
        $restriction = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_PRESET_RESTRICTION, '');
        if ('' === $restriction) {
            return [];
        }
        $restriction = explode(';', $restriction);
        array_walk(
            $restriction,
            function ($element) {
                return trim($element);
            }
        );

        return $restriction;
    }

    /**
     * @param ImageCropDataModel|null $activeCrop
     *
     * @return array
     *
     * @throws ImageCropEditorException
     */
    private function getCustomCropList($activeCrop = null)
    {
        $customCropList = [];
        $presetSystemName = $this->getPresetSystemName();
        $presetRestriction = $this->getPresetRestriction();
        $parameters = $this->getUrlParameters();
        unset($parameters[self::URL_PARAM_PRESET_NAME]);

        if (0 !== count($presetRestriction)) {
            return $customCropList;
        }

        $customCropList[] = [
            'name' => $this->translator->trans('chameleon_system_image_crop.editor.custom_crop_new'),
            'url' => URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&'),
            'active' => null === $presetSystemName && null === $activeCrop,
        ];

        $image = $this->getCmsImage();
        if (null === $image) {
            return $customCropList;
        }

        try {
            $crops = $this->imageCropDataAccess->getExistingCrops($this->getCmsImage());
            foreach ($crops as $crop) {
                $parameters[self::URL_PARAM_CROP_ID] = $crop->getId();
                $customCropList[] = [
                    'name' => $crop->getName(),
                    'url' => URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&'),
                    'active' => null !== $activeCrop && $crop->getId() === $activeCrop->getId(),
                ];
            }
        } catch (ImageCropDataAccessException $e) {
            throw new ImageCropEditorException($e->getMessage(), 0, $e);
        }

        return $customCropList;
    }

    /**
     * @param CmsMediaDataModel|null $cmsImage
     *
     * @return string
     */
    private function generateUrlToGetImage($cmsImage)
    {
        $parameters = [
            'pagedef' => self::PAGEDEF_NAME,
            self::URL_PARAM_IMAGE_ID => null !== $cmsImage ? $cmsImage->getId() : '1',
            '_pagedefType' => self::PAGEDEF_TYPE,
            'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'],
            '_fnc' => 'getImageFieldInformation',
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = '
            <link  href="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimagecrop/cropper-1-6-2-dist/cropper.min.css').'" rel="stylesheet">
            <link  href="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimagecrop/css/imageCropEditor.css').'" rel="stylesheet">
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimagecrop/cropper-1-6-2-dist/cropper.min.js').'"></script>
        ';

        return $includes;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlFooterIncludes()
    {
        $includes = parent::GetHtmlFooterIncludes();
        $includes[] = '
            <script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemimagecrop/js/imageCropEditor.js?v1').'"></script>
        ';

        return $includes;
    }

    /**
     * @return void
     */
    public function getImageFieldInformation()
    {
        $return = [];

        $imageId = $this->inputFilterUtil->getFilteredGetInput('imageId');
        $cropId = $this->inputFilterUtil->getFilteredGetInput('cropId');
        $crop = $this->imageCropDataAccess->getImageCropById(
            $cropId,
            $this->backendSession->getCurrentEditLanguageId()
        );

        if (null === $imageId || null === $crop) {
            $return['errorMessage'] = $this->translator->trans(
                'chameleon_system_image_crop.editor.image_or_crop_not_found'
            );
            $this->returnAsAjaxError($return);
        }

        $url = null;
        $croppedImage = $this->cropImageService->getCroppedImageForCmsMediaIdAndCropId(
            $imageId,
            $cropId,
            $this->backendSession->getCurrentEditLanguageId()
        );
        if (null !== $croppedImage) {
            $url = $croppedImage->getImageUrl();
        }
        $return['imageUrl'] = $url;

        $cropName = $crop->getName();
        $preset = $crop->getImageCropPreset();
        if (null !== $preset) {
            $cropName = ('' === $cropName) ? $preset->getName() : $cropName.' ('.$preset->getName().')';
        }
        $return['cropName'] = $cropName;

        $this->returnAsAjaxResponse($return);
    }

    /**
     * @param array $returnValues
     *
     * @return never
     */
    private function returnAsAjaxError($returnValues)
    {
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json');
        echo json_encode($returnValues);
        exit;
    }

    /**
     * @param array $returnValues
     *
     * @return never
     */
    private function returnAsAjaxResponse($returnValues)
    {
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/json');
        echo json_encode($returnValues);
        exit;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'saveCrop';
        $this->methodCallAllowed[] = 'getImageFieldInformation';
        $this->methodCallAllowed[] = 'deleteCrop';
    }

    /**
     * @return never
     */
    protected function saveCrop()
    {
        $cmsImage = $this->getCmsImage();

        $imageCrop = new ImageCropDataModel(
            null,
            $cmsImage,
            (int) $this->inputFilterUtil->getFilteredPostInput('pos_x'),
            (int) $this->inputFilterUtil->getFilteredPostInput('pos_y'),
            (int) $this->inputFilterUtil->getFilteredPostInput('width'),
            (int) $this->inputFilterUtil->getFilteredPostInput('height')
        );

        $preset = $this->getPreset();
        if (null !== $preset) {
            $imageCrop->setImageCropPreset($preset);
        }

        $imageCrop->setName($this->inputFilterUtil->getFilteredPostInput('name', ''));

        $cropId = $this->inputFilterUtil->getFilteredPostInput(self::URL_PARAM_CROP_ID);
        if (null === $cropId) {
            $existingCrop = $this->getExistingCrop($imageCrop);
            if (null !== $existingCrop) {
                $cropId = $existingCrop->getId();
            }
        }

        try {
            if (null !== $cropId) {
                $imageCrop->setId($this->inputFilterUtil->getFilteredPostInput(self::URL_PARAM_CROP_ID));
                $this->imageCropDataAccess->updateImageCrop($imageCrop);
            } else {
                $cropId = $this->imageCropDataAccess->insertImageCrop($imageCrop);
            }
        } catch (ImageCropDataAccessException $e) {
            $this->flashMessageService->addMessage(
                \TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
                'IMAGE-CROP-SAVE-FAILED'
            );
            $parameters = $this->getUrlParameters();
            if (null !== $cropId) {
                $parameters[self::URL_PARAM_CROP_ID] = $cropId;
            }
            $this->redirectService->redirect(
                URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&')
            );

            return;
        }

        $parameters = $this->getUrlParameters();

        $parameters[self::URL_PARAM_SAVED] = 1;
        $parameters[self::URL_PARAM_CROP_ID] = $cropId;

        $this->redirectService->redirect(
            URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&')
        );
    }

    /**
     * @return void
     */
    protected function deleteCrop()
    {
        $cropId = $this->inputFilterUtil->getFilteredInput(self::URL_PARAM_CROP_ID);
        if (null === $cropId) {
            $return = [
                'errorMessage' => $this->translator->trans(
                    'chameleon_system_image_crop.editor.crop_not_deleted'
                ),
            ];
            $this->returnAsAjaxError($return);
        }

        try {
            $this->imageCropDataAccess->deleteCrop($cropId);
            $return = [];
            $this->returnAsAjaxResponse($return);
        } catch (ImageCropDataAccessException $e) {
            $return = [
                'errorMessage' => $this->translator->trans(
                    'chameleon_system_image_crop.editor.crop_not_deleted'
                ),
            ];
            $this->returnAsAjaxError($return);
        }
    }

    /**
     * @return ImageCropDataModel|null
     */
    private function getExistingCrop(ImageCropDataModel $imageCrop)
    {
        $existingCrop = null;
        if (null !== $imageCrop->getImageCropPreset()) {
            $existingCrop = $this->imageCropDataAccess->getImageCrop(
                $imageCrop->getCmsMedia(),
                $imageCrop->getImageCropPreset()
            );
        }

        return $existingCrop;
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }
}
