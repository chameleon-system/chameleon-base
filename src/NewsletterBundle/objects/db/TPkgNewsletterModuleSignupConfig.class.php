<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgNewsletterModuleSignupConfig extends TPkgNewsletterModuleSignupConfigAutoParent
{
    /**
     * Anmeldung möglich für
     * Für welche Newsletter kann man sich über diese Modulinstanz anmelden.
     *
     * @param string $sOrderBy - an sql order by string (without the order by)
     *
     * @return TdbPkgNewsletterGroupList
     */
    public function GetFieldPkgNewsletterGroupList($sOrderBy = '')
    {
        $oPortal = TTools::GetActivePortal();
        $sSelect = "SELECT `pkg_newsletter_group`.*
                      FROM `pkg_newsletter_group`
                INNER JOIN `pkg_newsletter_module_signup_config_pkg_newsletter_group_mlt` ON `pkg_newsletter_group`.`id` = `pkg_newsletter_module_signup_config_pkg_newsletter_group_mlt`.`target_id`
                     WHERE `pkg_newsletter_module_signup_config_pkg_newsletter_group_mlt`.`source_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                       AND (
                                `pkg_newsletter_group`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPortal->id)."'
                                OR
                                `pkg_newsletter_group`.`cms_portal_id` = ''
                            )
                   ";
        if ('' !== $sOrderBy) {
            $sSelect .= ' ORDER BY '.$sOrderBy;
        } else {
            $sSelect .= ' ORDER BY `pkg_newsletter_group`.`name` ASC';
        }
        $oNewsletterGroupList = TdbPkgNewsletterGroupList::GetList($sSelect);

        return $oNewsletterGroupList;
    }
}
