<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

trait DomainSupportsLanguageTrait
{
    private function domainSupportsLanguage(
        \TdbCmsPortalDomains $domain,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ): bool {
        $additionalLanguageIds = true === $portal->fieldUseMultilanguage ? $domain->GetFieldCmsLanguageIdList() : [];

        if (in_array($language->id, $additionalLanguageIds, true)) {
            return true;
        }

        if ($language->id === $domain->fieldCmsLanguageId) {
            return true;
        }

        if ('' !== $domain->fieldCmsLanguageId) {
            return false;
        }

        $portalDefaultLanguageId = '' !== $portal->fieldCmsLanguageId ? $portal->fieldCmsLanguageId : \TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId;

        if ($language->id === $portalDefaultLanguageId) {
            return true;
        }

        return [] === $additionalLanguageIds
            && in_array($language->id, $portal->GetFieldCmsLanguageIdList(), true);
    }
}
