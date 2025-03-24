<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class TCMSModelBase extends TModelBase
{
    /**
     * returns true if the module may add its url to the history object.
     *
     * @return bool
     */
    protected function AllowAddingURLToHistory()
    {
        // if this is an ajax call, then prevent the object from being added to the history
        $allowHistory = false === $this->_UserMadeAjaxCall();
        $suppressHistoryCommand = (in_array('_suppressHistory', $this->aModuleConfig) && true == $this->aModuleConfig['_suppressHistory']);

        return $allowHistory && false === $suppressHistoryCommand;
    }

    /**
     * checks if the call was made via ajax.
     *
     * @return bool
     */
    protected function _UserMadeAjaxCall()
    {
        static $isAjaxCall;
        if (true !== $isAjaxCall && false !== $isAjaxCall) {
            $aUserData = $this->global->GetUserData();
            $isAjaxCall = false;
            if (isset($aUserData['module_fnc']) && is_array($aUserData['module_fnc'])) {
                $isAjaxCall = (false !== array_search('ExecuteAjaxCall', $aUserData['module_fnc']));
            }
        }

        return $isAjaxCall;
    }

    /**
     * {@inheritdoc}
     */
    public function ExecuteAjaxCall()
    {
        $methodName = $this->global->GetUserData('_fnc');
        if (empty($methodName)) {
            trigger_error('Ajax call made, but no function passed via _fnc', E_USER_ERROR);
        } else {
            if ($this->global->UserDataExists('_fieldName') && $this->global->UserDataExists('callFieldMethod')) {
                // if the module is a table editor and a _fieldName was sent, the AJAX call will be redirected to the TCMSField class
                $fieldName = $this->global->GetUserData('_fieldName');
                if (method_exists($this, 'ExecuteAjaxCallInField')) {
                    $functionResult = $this->ExecuteAjaxCallInField($fieldName);
                } else {
                    $functionResult = $this->_CallMethod($methodName);
                }
            } else {
                $functionResult = $this->_CallMethod($methodName);
            }
            $this->OutPutAjaxCallResult($functionResult);
        }
    }

    /**
     * outputs the ajax call result.
     */
    protected function OutPutAjaxCallResult($functionResult)
    {
        $outputMode = 'Ajax'; // JSON is the default output mode
        $permittedOutputModes = ['Ajax', 'Plain'];
        if ($this->global->UserDataExists('sOutputMode') && in_array($this->global->GetUserData('sOutputMode'), $permittedOutputModes)) {
            $outputMode = $this->global->GetUserData('sOutputMode');
        }
        switch ($outputMode) {
            case 'Plain':
                $this->_OutputForAjaxPlain($functionResult);
                break;
            case 'Ajax':
            default:
                $this->_OutputForAjax($functionResult);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Execute()
    {
        parent::Execute();
        $securityHelper = $this->getSecurityHelperAccess();

        $user = $securityHelper->getUser();
        $userObject = null;
        if (null !== $user) {
            $userObject = TdbCmsUser::GetNewInstance();
            $userObject->Load($user->getId());
        }

        $this->data['oCMSUser'] = $userObject;
        $oConfig = TdbCmsConfig::GetInstance();
        $this->data['sThemePath'] = $oConfig->GetThemeURL();

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        static $includes = null;
        if (null !== $includes) {
            return $includes;
        }
        $includes = parent::GetHtmlHeadIncludes();
        $securityHelper = $this->getSecurityHelperAccess();

        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return $includes;
        }
        $request = $this->getCurrentRequest();
        if (null === $request) {
            return $includes;
        }

        $url = $this->getBackendRouter()->generate('backend_js_translation_database');
        $includes[] = '<script src="'.TGlobal::OutHTML($url).'" type="text/javascript"></script>';
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/i18n/i18n.js').'" type="text/javascript"></script>';
        $locale = TGlobal::OutJS($request->getLocale());
        $includes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/i18n/i18n.'.$locale.'.js').'" type="text/javascript"></script>';

        return array_merge($includes, $includes);
    }

    protected function getSecurityHelperAccess(): SecurityHelperAccess
    {
        return ServiceLocator::get(SecurityHelperAccess::class);
    }

    private function getBackendRouter(): RouterInterface
    {
        return ServiceLocator::get('chameleon_system_core.router.chameleon_backend');
    }

    private function getCurrentRequest(): ?Request
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
