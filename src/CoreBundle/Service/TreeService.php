<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsTreeInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TreeService implements TreeServiceInterface
{
    /**
     * @var PageServiceInterface
     */
    private $pageService;
    /**
     * @var DataAccessCmsTreeInterface
     */
    private $dataAccess;
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    public function __construct(PageServiceInterface $pageService, DataAccessCmsTreeInterface $dataAccess, UrlUtil $urlUtil)
    {
        $this->pageService = $pageService;
        $this->dataAccess = $dataAccess;
        $this->urlUtil = $urlUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($treeId, $languageId = null)
    {
        $treeList = $this->dataAccess->loadAll($languageId);
        if (!isset($treeList[$treeId])) {
            return null;
        }

        return $treeList[$treeId];
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren($treeId, $includeHidden = false, $languageId = null)
    {
        /**
         * @var \TdbCmsTree[] $treeList
         */
        $treeList = $this->dataAccess->loadAll($languageId);
        $children = [];
        foreach ($treeList as $tree) {
            if ($treeId === $tree->fieldParentId && ($includeHidden || false === $tree->fieldHidden)) {
                $children[] = $tree;
            }
        }

        /*
         * @psalm-suppress InvalidArgument
         * @FIXME returning `null` from a sorting method is not allowed, should probably return `0`.
         */
        usort($children, function (\TdbCmsTree $a, \TdbCmsTree $b) {
            if ($a->fieldEntrySort === $b->fieldEntrySort) {
                return null;
            }

            return ($a->fieldEntrySort < $b->fieldEntrySort) ? -1 : 1;
        });

        $iterator = new \TIterator();
        foreach ($children as $child) {
            $iterator->AddItem($child);
        }
        $iterator->GoToStart();

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageForTreeRelative(\TdbCmsTree $tree, array $parameters = [], ?\TdbCmsLanguage $language = null)
    {
        if (!empty($tree->fieldLink)) {
            $url = $this->urlUtil->encodeUrlParts($tree->fieldLink);
            $url = $tree->replacePlaceHolderInURL($url);

            return $url;
        }
        $page = $tree->GetLinkedPageObject(true);
        if (false === $page) {
            throw new RouteNotFoundException('No page found for tree with ID '.$tree->id);
        }

        $url = $this->pageService->getLinkToPageObjectRelative($page, $parameters, $language);
        $url = $tree->replacePlaceHolderInURL($url);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageForTreeAbsolute(\TdbCmsTree $tree, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false)
    {
        if (!empty($tree->fieldLink)) {
            return $this->urlUtil->encodeUrlParts($tree->fieldLink);
        }
        $page = $tree->GetLinkedPageObject(true);
        if (false === $page) {
            throw new RouteNotFoundException('No page found for tree with ID '.$tree->id);
        }

        $url = $this->pageService->getLinkToPageObjectAbsolute($page, $parameters, $language);
        $url = $tree->replacePlaceHolderInURL($url);

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvertedNoFollowRulePageIds($treeId)
    {
        return $this->dataAccess->getInvertedNoFollowRulePageIds($treeId);
    }
}
