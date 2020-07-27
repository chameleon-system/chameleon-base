<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\Sidebar;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsMasterPagedefInterface;
use ChameleonSystem\CoreBundle\Security\PageAccessCheckInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

class CustomMenuItemProvider implements MenuItemProviderInterface
{
    /**
     * @var PageAccessCheckInterface
     */
    private $pageAccessCheck;

    /**
     * @var DataAccessCmsMasterPagedefInterface
     */
    private $accessCmsMasterPagedef;

    /**
     * @var UrlUtil
     */
    private $urlUtil;

    public function __construct(
        PageAccessCheckInterface $pageAccessCheck,
        DataAccessCmsMasterPagedefInterface $accessCmsMasterPagedef,
        UrlUtil $urlUtil
    ) {
        $this->pageAccessCheck = $pageAccessCheck;
        $this->accessCmsMasterPagedef = $accessCmsMasterPagedef;
        $this->urlUtil = $urlUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function createMenuItem(\TdbCmsMenuItem $menuItem): ?MenuItem
    {
        $customItem = new \TdbCmsMenuCustomItem($menuItem->fieldTarget);

        if (false === $this->isItemAccessAllowed($customItem)) {
            return null;
        }

        return new MenuItem(
            $menuItem->id,
            $menuItem->fieldName,
            $menuItem->fieldIconFontCssClass,
            $customItem->fieldUrl
        );
    }

    private function isItemAccessAllowed(\TdbCmsMenuCustomItem $customItem): bool
    {
        $activeUser = \TCMSUser::GetActiveUser();
        if (null === $activeUser) {
            return false;
        }

        $urlParameters = $this->urlUtil->getUrlParametersAsArray($customItem->fieldUrl);

        $pagedefParam = $urlParameters['pagedef'] ?? null;

        if (null === $pagedefParam) {
            return true; // only pages can be restricted
        }

        $pagedefType = $urlParameters['_pagedefType'] ?? 'Core'; // NOTE the default duplicates DataAccessCmsMasterPagedefFile::getPageDefinitionFilePath

        $pagedef = $this->accessCmsMasterPagedef->get($pagedefParam, $pagedefType);

        if (null === $pagedef) {
            return true; // bogus but not to judge here
        }

        return $this->pageAccessCheck->checkPageAccess($activeUser, $pagedef);
    }
}
