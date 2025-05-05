<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\UniversalUploader\Interfaces\UploaderPluginIntegrationServiceInterface;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel;
use ChameleonSystem\CoreBundle\UniversalUploader\Library\UploaderParameterServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CMSModuleUniversalUploader extends TCMSModelBase
{
    /**
     * the table editor object that handles the uploads (TCMSTableEditorMedia or TCMSTableEditorDocument).
     *
     * @var TCMSTableEditorManager
     */
    protected $oTableEditorManager;

    /**
     * pass config params to view.
     *
     * @return array
     */
    public function Execute()
    {
        $this->data = parent::Execute();
        $this->data['uploaderFormAction'] = $this->getUploaderFormAction();
        $this->data['uploadUrl'] = $this->getUploadUrl();
        $this->data['hasError'] = false;
        $this->data['errorMessage'] = '';

        try {
            $parameterBag = $this->getParameterBag();
            $parameterBag->validate();

            $configuration = $this->getUploaderConfiguration();
            $this->data['chunkSize'] = $configuration->getChunkSize();
            $this->data['maxUploadSize'] = $configuration->getMaxUploadSize($parameterBag);

            if (null === $parameterBag->getAllowedFileTypes()) {
                $parameterBag->setAllowedFileTypes($configuration->getAllowedFileTypes($parameterBag));
            }

            $this->data['hiddenFields'] = $this->getHiddenFields($parameterBag);
            $this->data['parameterBag'] = $parameterBag;
        } catch (Exception $e) {
            $this->data['hasError'] = true;
            if ($e instanceof ChameleonSystem\CoreBundle\UniversalUploader\Exception\InvalidParameterValueException) {
                $this->data['errorMessage'] = $this->getTranslator()->trans('chameleon_system_core.cms_module_universal_uploader.invalid_parameter', [], ChameleonSystem\CoreBundle\i18n\TranslationConstants::DOMAIN_BACKEND);
            } else {
                $this->data['errorMessage'] = $e->getMessage();
            }
        }

        return $this->data;
    }

    private function getUploaderConfiguration()
    {
        $configuration = new ChameleonSystem\CoreBundle\UniversalUploader\Bridge\Chameleon\UploaderConfiguration(TdbCmsConfig::GetInstance());

        return $configuration;
    }

    /**
     * @return string
     */
    private function getUploaderFormAction()
    {
        return PATH_CMS_CONTROLLER; // we add all the parameters like pagedef as hidden input fields because form is sent via post
    }

    private function getUploadUrl()
    {
        return $this->getRouter()->generate('universal_uploader_upload');
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('router');
    }

    /**
     * @return UploaderParameterServiceInterface
     */
    private function getUploaderParameterService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.universal_uploader.uploader_parameter_service');
    }

    /**
     * @return UploaderParametersDataModel
     */
    private function getParameterBag()
    {
        return $this->getUploaderParameterService()->getParameters();
    }

    /**
     * @param UploaderParametersDataModel $parameterBag
     *
     * @return array
     */
    private function getHiddenFields($parameterBag)
    {
        $hiddenFields = [];
        $parameters = $parameterBag->getAsArray(['sUploadName', 'sUploadDescription']);
        foreach ($parameters as $parameterName => $parameterValue) {
            if (false === is_array($parameterValue) && false === is_object($parameterValue) && null !== $parameterValue) {
                $hiddenFields[] = ['name' => $parameterName, 'value' => $parameterValue];
            }
        }

        $hiddenFields[] = ['name' => 'pagedef', 'value' => 'CMSUniversalUploader'];
        $hiddenFields[] = ['name' => '_pagedefType', 'value' => $this->getPagedefType()];

        return $hiddenFields;
    }

    /**
     * @return string
     */
    private function getPagedefType()
    {
        return $this->getRequest()->get('_pagedefType', 'Core');
    }

    /**
     * @return Request|null
     */
    private function getRequest()
    {
        /**
         * @var RequestStack $requestStack
         */
        $requestStack = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack');

        return $requestStack->getCurrentRequest();
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();

        $externalFunctions = ['GetDownloadHTML'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $integrationService = $this->getPluginIntegrationService();
        $aIncludes = array_merge($aIncludes, $integrationService->getHtmlHeadIncludes());

        return $aIncludes;
    }

    /**
     * @return UploaderPluginIntegrationServiceInterface
     */
    private function getPluginIntegrationService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.universal_uploader.plugin_integration_service');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * Not sure, why this method was put here...
     *
     * method is used to return the download link when selecting a new download for the download field
     * called via (ajax) javascript
     *
     * @return string
     */
    public function GetDownloadHTML()
    {
        $returnVal = '';
        $sDocumentID = $this->global->GetUserData('documentID');
        if (!empty($sDocumentID) && 'undefined' != $sDocumentID && !is_null($sDocumentID)) {
            $oRecord = new TCMSDownloadFile();
            /* @var $oRecord TCMSDownloadFile* */
            $oRecord->table = 'cms_document';
            $oRecord->Load($this->global->GetUserData('documentID'));
            $returnVal = $oRecord->getDownloadHtmlTag();
        }

        return $returnVal;
    }
}
