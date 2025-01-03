<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

/**
 * overwrite the module loader so we can set the instance of the module as well.
 * /**/
class TUserModuleLoader extends TModuleLoader
{
    /**
     * {@inheritdoc}
     *
     * note: you can force a custom user model to act like a plain user model (ie no modulechooser)
     *       by passing 'static'=>true in the pagedef to the model
     */
    protected function _SetModuleConfigData($name, $config, $templateLanguage = null)
    {
        // depending on the request we may need to change the model to a "pick the module" instance
        // we do this if the request came from the cms, and an url parameter is present.

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $isModuleChooserRequest = $request->get('__modulechooser');
        $isMasterPagedefRequest = $request->get('__masterPageDef', false);

        $requestModuleChooser = 'true' === $isModuleChooserRequest;
        $forceStatic = (array_key_exists('static', $config) && true === $config['static']);

        if (!$forceStatic && $requestModuleChooser && $this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER) && self::ClassIsCustomModule($config['model'])) {
            // need to check if the module has been overwritten using the cms config.
            $cmsConfig = TdbCmsConfig::GetInstance();

            if (null === $cmsConfig) {
                return null;
            }

            $sModuleClassName = 'CMSModuleChooser';

            $sMappedClassName = $cmsConfig->GetRealModuleClassName($sModuleClassName);
            if (false !== $sMappedClassName) {
                $sModuleClassName = $sMappedClassName;
            }
            $tmpModel = $this->CreateModuleInstance($sModuleClassName);
            $tmpModel->oCustomerModelObject = parent::_SetModuleConfigData($name, $config);
            // instanceID is optional. sometimes (like the MTExtranet) we do not have an instance id
            if (isset($config['instanceID'])) {
                $tmpModel->oCustomerModelObject->instanceID = $config['instanceID'];
            }
            $tmpModel->viewTemplate = PATH_CORE_MODULES.'CMSModuleChooser/views/standard.view.php';
            $tmpModel->bMasterPagedefRequest = $isMasterPagedefRequest;
            $tmpModel->sModuleSpotName = $name;
            $tmpModel->aModuleConfig = $config;
            if (!is_null($templateLanguage) && property_exists($tmpModel, 'templateLanguage')) {
                $tmpModel->templateLanguage = $templateLanguage;
            }
        } else {
            /** @var $tmpModel TUserModelBase */
            $tmpModel = parent::_SetModuleConfigData($name, $config, $templateLanguage);
        }
        if (array_key_exists('instanceID', $config)) {
            $tmpModel->instanceID = $config['instanceID'];
        }

        return $tmpModel;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    protected function ClassIsCustomModule($class)
    {
        return $this->moduleResolver->hasModule($class) || is_subclass_of($class, 'TUserCustomModelBaseCore');
    }
}
