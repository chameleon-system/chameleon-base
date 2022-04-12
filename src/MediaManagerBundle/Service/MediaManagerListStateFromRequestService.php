<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Service;

use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaManagerListStateServiceInterface;
use ChameleonSystem\MediaManager\MediaManagerListState;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MediaManagerListStateFromRequestService implements MediaManagerListStateServiceInterface
{
    const SESSION_KEY = 'mediaManagerState';

    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var int
     */
    private $defaultPageSize;

    /**
     * @param InputFilterUtilInterface $inputFilterUtil
     * @param RequestStack             $requestStack
     * @param int                      $defaultPageSize
     */
    public function __construct(InputFilterUtilInterface $inputFilterUtil, RequestStack $requestStack, $defaultPageSize)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->requestStack = $requestStack;
        $this->defaultPageSize = (int) $defaultPageSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getListState()
    {
        $state = $this->getStateFromSession();
        if (null === $state) {
            $state = $this->createDefaultState();
        }

        $searchTerm = $this->inputFilterUtil->getFilteredInput(
            MediaManagerListState::STATE_PARAM_NAME_SEARCH_TERM
        );
        if (null !== $searchTerm) {
            $state->setSearchTerm($searchTerm);
        }

        $pageSize = $this->inputFilterUtil->getFilteredInput(MediaManagerListState::STATE_PARAM_NAME_PAGE_SIZE);
        if (null !== $pageSize) {
            $state->setPageSize($pageSize);
        }

        $pageNumber = $this->inputFilterUtil->getFilteredInput(MediaManagerListState::STATE_PARAM_NAME_PAGE);
        if (null !== $pageNumber) {
            $state->setPageNumber($pageNumber);
        }

        $mediaTreeNodeId = $this->inputFilterUtil->getFilteredInput(
            MediaManagerListState::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID
        );
        if (null !== $mediaTreeNodeId) {
            $state->setMediaTreeNodeId($mediaTreeNodeId);
        }

        $listView = $this->inputFilterUtil->getFilteredInput(MediaManagerListState::STATE_PARAM_NAME_LIST_VIEW);
        if (null !== $listView) {
            $state->setListView($listView);
        }

        $showSubtree = $this->inputFilterUtil->getFilteredInput(
            MediaManagerListState::STATE_PARAM_NAME_SHOW_SUBTREE
        );
        if (null !== $showSubtree) {
            $state->setShowSubtree('1' === $showSubtree);
        }

        $deleteWithUsageSearch = $this->inputFilterUtil->getFilteredInput(
            MediaManagerListState::STATE_PARAM_NAME_DELETE_WITH_USAGE_SEARCH
        );
        if (null !== $deleteWithUsageSearch) {
            $state->setDeleteWithUsageSearch('1' === $deleteWithUsageSearch);
        }

        $sortColumn = $this->inputFilterUtil->getFilteredInput(MediaManagerListState::STATE_PARAM_NAME_SORT);
        if (null !== $sortColumn) {
            $state->setSortColumn($sortColumn);
        }

        $isPickImageMode = $this->isPickImageMode();
        $state->setPickImageMode($isPickImageMode, $this->getPickImageCallback(), $this->isPickImageWithCrop());

        $this->saveStateToSession($state);

        return $state;
    }

    private function getStateFromSession(): ?MediaManagerListState
    {
        $session = $this->getSession();

        if (null === $session) {
            return null;
        }

        return $session->get(self::SESSION_KEY);
    }

    private function saveStateToSession(MediaManagerListState $state): void
    {
        $session = $this->getSession();

        if (null === $session) {
            return;
        }

        $session->set(self::SESSION_KEY, $state);
    }

    private function getSession(): ?SessionInterface
    {
        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return null;
        }
        if (false === $request->hasSession()) {
            return null;
        }
        $session = $request->getSession();
        if (false === $session->isStarted()) {
            return null;
        }

        return $session;
    }

    /**
     * @return MediaManagerListState
     */
    private function createDefaultState()
    {
        $state = new MediaManagerListState();
        $state->setPageSize($this->getDefaultPageSize());
        $state->setPageNumber(0);
        $state->setListView('grid');
        $state->setShowSubtree(true);
        $state->setDeleteWithUsageSearch(true);

        return $state;
    }

    /**
     * @return int
     */
    private function getDefaultPageSize()
    {
        return $this->defaultPageSize;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    private function isPickImageMode()
    {
        return '1' === $this->inputFilterUtil->getFilteredInput(MediaManagerListState::URL_NAME_PICK_IMAGE_MODE);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    private function getPickImageCallback()
    {
        return $this->inputFilterUtil->getFilteredInput(
            MediaManagerListState::URL_NAME_PICK_IMAGE_CALLBACK,
            '_SetImage'
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    private function isPickImageWithCrop()
    {
        return '1' === $this->inputFilterUtil->getFilteredInput(MediaManagerListState::URL_NAME_PICK_IMAGE_WITH_CROP);
    }
}
