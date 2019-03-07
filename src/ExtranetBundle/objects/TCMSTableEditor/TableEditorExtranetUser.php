<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TableEditorExtranetUser extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    public function DefineInterface()
    {
        parent::DefineInterface();

        $this->methodCallAllowed[] = 'LoginAsExtranetUser';
    }

    /**
     * {@inheritdoc}
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        $cmsUser = TCMSUser::GetActiveUser();
        if (!$cmsUser || !$cmsUser->oAccessManager || !$cmsUser->oAccessManager->PermitFunction('allow-login-as-extranet-user')) {
            return;
        }
        $menuItem = new TCMSTableEditorMenuItem();
        $menuItem->sItemKey = 'ExtranetUserLogin';
        $menuItem->setTitle($this->getTranslator()->trans('chameleon_system_extranet.action.login_as_extranet_user', array(), TranslationConstants::DOMAIN_BACKEND));
        $menuItem->sIcon = 'fas fa-user-check';

        $executingModulePointer = $this->getGlobal()->GetExecutingModulePointer();
        $pagedef = $this->getInputFilterUtil()->getFilteredInput('pagedef');
        $urlData = array(
            'module_fnc' => array($executingModulePointer->sModuleSpotName => 'LoginAsExtranetUser'),
            '_noModuleFunction' => 'true',
            'pagedef' => $pagedef,
            'id' => $this->sId,
            'tableid' => $this->oTableConf->id,
        );
        $url = PATH_CMS_CONTROLLER.$this->getUrlUtil()->getArrayAsUrl($urlData, '?', '&');
        $js = "var tmp = window.open('{$url}');";
        $menuItem->sOnClick = $js;

        $this->oMenuItems->AddItem($menuItem);
    }

    /**
     * Login as the currently selected extranet user (permissions needed).
     */
    public function LoginAsExtranetUser()
    {
        $cmsUser = TCMSUser::GetActiveUser();
        if (!$cmsUser || !$cmsUser->oAccessManager || !$cmsUser->oAccessManager->PermitFunction('allow-login-as-extranet-user')) {
            return;
        }
        $inputFilterUtil = $this->getInputFilterUtil();
        $userId = $inputFilterUtil->getFilteredGetInput('id');
        if (null === $userId) {
            return;
        }
        $extranetUserProvider = $this->getExtranetUserProvider();
        $extranetUserProvider->reset();
        $extranetUser = $extranetUserProvider->getActiveUser();
        $extranetUser->Load($userId);
        if (empty($extranetUser->fieldCmsPortalId)) {
            $portalList = TdbCmsPortalList::GetList();
            $portalList->GoToStart();
            $portal = $portalList->Current();
        } else {
            $portal = TdbCmsPortal::GetNewInstance($extranetUser->fieldCmsPortalId);
        }
        $extranetUser->DirectLoginWithoutPassword($extranetUser->GetName(), $portal->id);
        $url = $this->getPageService()->getLinkToPortalHomePageRelative(array(), $portal);
        $this->getRedirect()->redirect($url);
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }
}
