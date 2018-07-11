<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler;

/**
 * @deprecated since 6.2.0 - GenerateCssController is used instead.
 */
class TPkgViewRenderer_TCMSSmartURLHandler_SnippetLessCompiler extends TCMSSmartURLHandler
{
    /**
     * {@inheritdoc}
     */
    public function GetPageDef()
    {
        $lessCompiler = $this->getLessCompiler();
        $portal = $this->getPortalForCssRequest($lessCompiler->getCompiledCssFilenamePattern());

        if (null === $portal) {
            return false;
        }

        $css = $lessCompiler->getGeneratedCssForPortal($portal);

        if ($this->getLessCachingForWebserverEnabled()) {
            $lessCompiler->writeCssFileForPortal($css, $portal);
        }

        header('Content-Type: text/css');
        echo $css;
        exit(0);
    }

    /**
     * @param string $cssFilenamePattern
     *
     * @return null|TdbCmsPortal
     */
    private function getPortalForCssRequest($cssFilenamePattern)
    {
        $sPath = $this->getRequestInfoService()->getPathInfoWithoutPortalAndLanguagePrefix();
        if ('/' != substr($sPath, 0, 1)) {
            $sPath = '/'.$sPath;
        }
        if (preg_match($cssFilenamePattern, $sPath, $aMatches)) {
            $iPortalCmsIdent = (int) $aMatches[2];
            $oPortal = TdbCmsPortal::GetNewInstance();
            if ($oPortal->LoadFromField('cmsident', $iPortalCmsIdent)) {
                return $oPortal;
            }
        }

        return null;
    }

    /**
     * @return RequestInfoServiceInterface
     */
    private function getRequestInfoService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.request_info_service');
    }

    /**
     * @return TPkgViewRendererLessCompiler
     */
    private function getLessCompiler()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.less_compiler');
    }

    /**
     * @return bool
     */
    private function getLessCachingForWebserverEnabled()
    {
        return true === \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.cache_less_files');
    }
}
