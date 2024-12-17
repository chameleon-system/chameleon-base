<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsMasterPagedefInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChameleonFrontendController extends ChameleonController
{
    private ContainerInterface $container;
    private \TPkgViewRendererConfigToLessMapper $configToLessMapper;
    private SecurityHelperAccess $securityHelperAccess;

    /**
     * @param \IViewPathManager $viewPathManager
     */
    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        DataAccessCmsMasterPagedefInterface $dataAccessCmsMasterPagedef,
        \TModuleLoader $moduleLoader,
        SecurityHelperAccess $securityHelperAccess,
        $viewPathManager,
        ContainerInterface $container,
        \TPkgViewRendererConfigToLessMapper $configToLessMapper
    ) {
        parent::__construct($requestStack, $eventDispatcher, $dataAccessCmsMasterPagedef, $moduleLoader, $securityHelperAccess, $viewPathManager);
        $this->securityHelperAccess = $securityHelperAccess;
        $this->container = $container; // for ViewRenderer instantiation
        $this->configToLessMapper = $configToLessMapper;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $this->accessCheckHook();
        $activePage = $this->activePageService->getActivePage();

        if (null === $activePage) {
            throw new NotFoundHttpException('No active page was found. At this point, this is most likely
                caused by a missing request attribute named "pagedef" specifying a valid page ID. If the caller of this
                method does not know which pagedef to set, throw a NotFoundHttpException instead.');
        }

        if (false === $activePage->AllowAccessByCurrentUser()) {
            throw new AccessDeniedHttpException('user has no access to the requested page');
        }

        return $this->GeneratePage($activePage->id);
    }

    /**
     * @return \TCMSPageDefinitionFile|\TCMSPagedef|\TdbCmsMasterPagedef|false
     */
    public function GetPagedefObject(string $pagedef)
    {
        // check if the pagedef exists in the database... if it does, use it. if not, use the file
        $oPageDefinitionFile = null;

        $inputFilterUtil = $this->getInputFilterUtil();
        $requestMasterPageDef = $inputFilterUtil->getFilteredInput('__masterPageDef', false);

        if ($this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            // load master pagedef...
            $oPageDefinitionFile = \TdbCmsMasterPagedef::GetNewInstance();
            $oPageDefinitionFile->Load($inputFilterUtil->getFilteredInput('id'));
        } else {
            $oPageDefinitionFile = new \TCMSPagedef($pagedef);

            if (empty($oPageDefinitionFile->iMasterPageDefId)) {
                $oPageDefinitionFile->sqlData = false;
            }
        }

        return $oPageDefinitionFile;
    }

    /**
     * {@inheritdoc}
     */
    protected function _GetCustomHeaderData($bAsArray = false)
    {
        $sCustomData = parent::_GetCustomHeaderData($bAsArray);
        $aNewLines = [];
        if ($bAsArray) {
            $aNewLines = $sCustomData;
        } else {
            $aNewLines[] = $sCustomData;
        }

        if (true === CMS_PKG_VIEW_RENDERER_ENABLE_LESS_COMPILER) {
            // should be moved into an event listener (CoreEvents::GLOBAL_HTML_HEADER_INCLUDE)
            $oViewRenderer = $this->container->get('chameleon_system_view_renderer.view_renderer');
            $oViewRenderer->AddMapper($this->configToLessMapper);
            $oViewRenderer->AddSourceObject('inTemplateEngineMode', 'true' === $this->getInputFilterUtil()->getFilteredInput('__modulechooser', false));
            $aNewLines[] = $oViewRenderer->Render('head-includes/less.html.twig');
        }

        if ($bAsArray) {
            return $aNewLines;
        }

        return implode("\n", $aNewLines);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $layoutTemplate - name of the layout template
     *
     * @return string|null
     */
    protected function LoadLayoutTemplate($layoutTemplate)
    {
        return $this->viewPathManager->getLayoutViewPath($layoutTemplate);
    }

    /**
     * {@inheritdoc}
     */
    protected function handleRequest($pagedef)
    {
        parent::handleRequest($pagedef);

        $request = $this->getRequest();

        if (null === $request) {
            return;
        }

        $aNonSeoParameter = $request->query->keys();

        $referrerPageId = $this->getInputFilterUtil()->getFilteredInput('refererPageId', null, false, \TCMSUserInput::FILTER_FILENAME);
        $this->activePageService->setActivePage($pagedef, $referrerPageId);

        $aAllParameter = $request->query->keys();
        $aSeoParameterList = array_diff($aAllParameter, $aNonSeoParameter);
        \TCMSSmartURLData::GetActive()->setSeoURLParameters($aSeoParameterList);
    }
}
