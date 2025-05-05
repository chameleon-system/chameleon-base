<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;

class TPkgCmsCoreLayoutPlugin_ThemeBlockLayout implements IPkgCmsCoreLayoutPlugin
{
    private $moduleLoader;

    public function __construct(TModuleLoader $oModuleLoader)
    {
        $this->moduleLoader = $oModuleLoader;
    }

    /**
     * {@inheritDoc}
     */
    public function run($contentIdentifier, $config)
    {
        $oThemeBlock = TdbPkgCmsThemeBlock::GetNewInstance();
        if ($oThemeBlock->LoadFromField('system_name', $contentIdentifier)) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
            if ($oPortal && !empty($oPortal->fieldPkgCmsThemeId)) {
                $sQuery = 'SELECT * FROM `pkg_cms_theme_block_layout`
                        INNER JOIN `pkg_cms_theme_pkg_cms_theme_block_layout_mlt` ON `pkg_cms_theme_pkg_cms_theme_block_layout_mlt`.`target_id` = `pkg_cms_theme_block_layout`.`id`
                        WHERE `pkg_cms_theme_pkg_cms_theme_block_layout_mlt`.`source_id` = :themeId
                        AND `pkg_cms_theme_block_layout`.`pkg_cms_theme_block_id` = :blockId
                        LIMIT 0,1
                        ';
                $row = $this->getDatabaseConnection()->fetchAssociative($sQuery, ['themeId' => $oPortal->fieldPkgCmsThemeId, 'blockId' => $oThemeBlock->id]);
                $oThemeBlockLayout = null;
                if (false === $row) {
                    $oThemeBlockLayout = $oThemeBlock->GetFieldPkgCmsThemeBlockLayout();
                } else {
                    $oThemeBlockLayout = TdbPkgCmsThemeBlockLayout::GetNewInstance($row);
                }
                if ($oThemeBlockLayout) {
                    $aBasePaths = $this->getBasePathsForBlockLayouts();
                    $sLayoutPath = '';
                    foreach ($aBasePaths as $sBasePath) {
                        $sLayoutPath = $sBasePath.'/'.$oThemeBlockLayout->fieldLayoutFile;
                        if (file_exists($sLayoutPath)) {
                            break;
                        }
                    }
                    $modules = $this->moduleLoader;
                    require TGlobal::ProtectedPath($sLayoutPath);
                }
            }
        }
    }

    /**
     * @param string $sSystemName
     *
     * @return string
     */
    public function RenderThemeBlock($sSystemName)
    {
        $sThemeBlock = '<!-- theme block '.TGlobal::OutHTML($sSystemName).' not found -->';
        $oThemeBlock = TdbPkgCmsThemeBlock::GetNewInstance();
        if ($oThemeBlock->LoadFromField('system_name', $sSystemName)) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
            if ($oPortal && !empty($oPortal->fieldPkgCmsThemeId)) {
                $sQuery = "SELECT * FROM `pkg_cms_theme_block_layout`
                        INNER JOIN `pkg_cms_theme_pkg_cms_theme_block_layout_mlt` ON `pkg_cms_theme_pkg_cms_theme_block_layout_mlt`.`target_id` = `pkg_cms_theme_block_layout`.`id`
                        WHERE `pkg_cms_theme_pkg_cms_theme_block_layout_mlt`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPortal->fieldPkgCmsThemeId)."'
                        AND `pkg_cms_theme_block_layout`.`pkg_cms_theme_block_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oThemeBlock->id)."'
                        ";
                $oThemeBlockLayoutList = TdbPkgCmsThemeBlockLayoutList::GetList($sQuery);
                $oThemeBlockLayout = $oThemeBlockLayoutList->Next();
                if (false === $oThemeBlockLayout) {
                    $oThemeBlockLayout = $oThemeBlock->GetFieldPkgCmsThemeBlockLayout();
                }
                if ($oThemeBlockLayout) {
                    $aBasePaths = $this->getBasePathsForBlockLayouts();
                    foreach ($aBasePaths as $sBasePath) {
                        $sLayoutPath = $sBasePath.'/'.$oThemeBlockLayout->fieldLayoutFile;
                        if (file_exists($sLayoutPath)) {
                            break;
                        }
                    }

                    ob_start();
                    $modules = $this;
                    require TGlobal::ProtectedPath($sLayoutPath);
                    $sThemeBlock = ob_get_contents();
                    ob_end_clean();
                }
            }
        }

        return $sThemeBlock;
    }

    protected function getBasePathsForBlockLayouts($oPortal = null)
    {
        $aBasePaths = $this->getViewRendererSnippetDirectory()->getBasePaths($oPortal, 'blockLayouts');

        return array_reverse($aBasePaths);
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return TPkgViewRendererSnippetDirectoryInterface
     */
    private function getViewRendererSnippetDirectory()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.snippet_directory');
    }
}
