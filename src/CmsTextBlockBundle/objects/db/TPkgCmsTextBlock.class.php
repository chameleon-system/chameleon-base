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

class TPkgCmsTextBlock extends TPkgCmsTextBlockAutoParent
{
    public const VIEW_PATH = 'pkgCmsTextBlock/views/db/TPkgCmsTextBlock';

    /**
     * Renders a text block.
     * You can set width of text block by adding "iWidth" to $aCallTimeVars.(default width 600).
     *
     * @param string $sViewName
     * @param string $sSubtype
     * @param int[] $aCallTimeVars
     *
     * @return string
     */
    public function Render($sViewName, $sSubtype = 'Customer', $aCallTimeVars = [])
    {
        $oView = new TViewParser();
        $iWidth = 600;
        if (array_key_exists('iWidth', $aCallTimeVars)) {
            $iWidth = $aCallTimeVars['iWidth'];
        }
        $oView->AddVarArray($aCallTimeVars);
        $oView->AddVar('placeholders', $aCallTimeVars);
        $oView->AddVar('iWidth', $iWidth);
        $oView->AddVar('oTextBlock', $this);

        return $oView->RenderObjectPackageView($sViewName, self::VIEW_PATH, $sSubtype);
    }

    /**
     * return text object for active portal.
     *
     * @param string $sSystemName
     * @param string|null $sPortalId - uses active portal if null is passed
     *
     * @return TdbPkgCmsTextBlock|null
     */
    public static function GetInstanceFromSystemName($sSystemName, $sPortalId = null)
    {
        static $aCache = [];
        if (array_key_exists($sSystemName, $aCache)) {
            return $aCache[$sSystemName];
        }

        if (null === $sPortalId) {
            $sPortalId = '';
            $activePortal = self::getPortalDomainService()->getActivePortal();
            if (null !== $activePortal) {
                $sPortalId = $activePortal->id;
            }
        }

        $oObject = null;
        $query = 'SELECT `pkg_cms_text_block`.*
              FROM `pkg_cms_text_block`
        INNER JOIN `pkg_cms_text_block_cms_portal_mlt` ON `pkg_cms_text_block`.`id` = `pkg_cms_text_block_cms_portal_mlt`.`source_id`
             WHERE `pkg_cms_text_block_cms_portal_mlt`.`target_id` = :portalId
               AND `pkg_cms_text_block`.`systemname` = :systemName
             LIMIT 0,1
                 ';
        $aRow = ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->fetchAssociative(
            $query,
            ['portalId' => $sPortalId, 'systemName' => $sSystemName]
        );
        if (false !== $aRow) {
            $oObject = TdbPkgCmsTextBlock::GetNewInstance($aRow);
        }
        $aCache[$sSystemName] = $oObject;

        return $aCache[$sSystemName];
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
