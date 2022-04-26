<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager;

class MediaManagerListState
{
    const STATE_PARAM_NAME_PAGE = 'p';

    const STATE_PARAM_NAME_PAGE_SIZE = 'ps';

    const STATE_PARAM_NAME_SEARCH_TERM = 's';

    const STATE_PARAM_NAME_MEDIA_TREE_NODE_ID = 'mediaTreeId';

    const STATE_PARAM_NAME_LIST_VIEW = 'listView';

    const STATE_PARAM_NAME_SHOW_SUBTREE = 'subtree';

    const STATE_PARAM_NAME_DELETE_WITH_USAGE_SEARCH = 'enableUsageSearch';

    const STATE_PARAM_NAME_SORT = 'sr';

    const URL_NAME_PICK_IMAGE_MODE = 'pickImage';

    const URL_NAME_PICK_IMAGE_CALLBACK = 'pickImageCallback';

    const URL_NAME_PICK_IMAGE_WITH_CROP = 'pickImageWithCrop';

    /**
     * @var array
     */
    private $stateParameters = array();

    /**
     * @param int $pageNumber
     *
     * @return void
     */
    public function setPageNumber($pageNumber)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_PAGE, (int) $pageNumber);
    }

    /**
     * @param string $stateParameterName
     * @param mixed  $stateParameterValue
     *
     * @return void
     */
    private function setStateParameter($stateParameterName, $stateParameterValue)
    {
        $this->stateParameters[$stateParameterName] = $stateParameterValue;
    }

    /**
     * @param int $pageSize
     *
     * @return void
     */
    public function setPageSize($pageSize)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_PAGE_SIZE, (int) $pageSize);
    }

    /**
     * @param string $searchTerm
     *
     * @return void
     */
    public function setSearchTerm($searchTerm)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_SEARCH_TERM, $searchTerm);
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setMediaTreeNodeId($id)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID, $id);
    }

    /**
     * @param string $listView
     *
     * @return void
     */
    public function setListView($listView)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_LIST_VIEW, $listView);
    }

    /**
     * @param bool $showSubtree
     *
     * @return void
     */
    public function setShowSubtree($showSubtree)
    {
        $this->setStateParameter(
            self::STATE_PARAM_NAME_SHOW_SUBTREE,
            $showSubtree
        );
    }

    /**
     * @param bool $deleteWithUsageSearch
     *
     * @return void
     */
    public function setDeleteWithUsageSearch($deleteWithUsageSearch)
    {
        $this->setStateParameter(
            self::STATE_PARAM_NAME_DELETE_WITH_USAGE_SEARCH,
            $deleteWithUsageSearch
        );
    }

    /**
     * @param string $sortColumnSystemName
     *
     * @return void
     */
    public function setSortColumn($sortColumnSystemName)
    {
        $this->setStateParameter(self::STATE_PARAM_NAME_SORT, $sortColumnSystemName);
    }

    /**
     * @param bool   $isPickImageMode
     * @param string $callback
     * @param bool   $hasCrop
     *
     * @return void
     */
    public function setPickImageMode($isPickImageMode, $callback, $hasCrop)
    {
        $this->setStateParameter(self::URL_NAME_PICK_IMAGE_MODE, $isPickImageMode);
        if ($isPickImageMode) {
            $this->setStateParameter(self::URL_NAME_PICK_IMAGE_CALLBACK, $callback);
            $this->setStateParameter(self::URL_NAME_PICK_IMAGE_WITH_CROP, $hasCrop);
        }
    }

    /**
     * @return int|null
     */
    public function getPageNumber()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_PAGE, 0);
    }

    /**
     * @param string     $stateParameterName
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    private function getStateParameter($stateParameterName, $default = null)
    {
        if (false === isset($this->stateParameters[$stateParameterName])) {
            return $default;
        }

        return $this->stateParameters[$stateParameterName];
    }

    /**
     * @return int|null
     */
    public function getPageSize()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_PAGE_SIZE, -1);
    }

    /**
     * @return string|null
     */
    public function getSearchTerm()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_SEARCH_TERM, '');
    }

    /**
     * @return string|null
     */
    public function getMediaTreeNodeId()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_MEDIA_TREE_NODE_ID);
    }

    /**
     * @return string|null
     */
    public function getListView()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_LIST_VIEW);
    }

    /**
     * @return bool|null
     */
    public function isShowSubtree()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_SHOW_SUBTREE, true);
    }

    /**
     * @return bool|null
     */
    public function isDeleteWithUsageSearch()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_DELETE_WITH_USAGE_SEARCH, true);
    }

    /**
     * @return string|null
     */
    public function getSortColumn()
    {
        return $this->getStateParameter(self::STATE_PARAM_NAME_SORT);
    }

    /**
     * @return bool|null
     */
    public function isPickImageMode()
    {
        return $this->getStateParameter(self::URL_NAME_PICK_IMAGE_MODE, false);
    }

    /**
     * @return string|null
     */
    public function getPickImageCallback()
    {
        return $this->getStateParameter(self::URL_NAME_PICK_IMAGE_CALLBACK, '_SetImage');
    }

    /**
     * @return bool|null
     */
    public function isPickImageWithCrop()
    {
        return $this->getStateParameter(self::URL_NAME_PICK_IMAGE_WITH_CROP, false);
    }

    /**
     * @param array $excludeParameters
     *
     * @return array
     */
    public function getStateParameters(array $excludeParameters = array())
    {
        return array_diff_key($this->stateParameters, array_flip($excludeParameters));
    }
}
