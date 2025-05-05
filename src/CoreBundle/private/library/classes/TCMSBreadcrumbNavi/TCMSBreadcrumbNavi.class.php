<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PageServiceInterface;

/**
 * fetches breadcrumb navi.
 * /**/
class TCMSBreadcrumbNavi
{
    /**
     * name of the navigation item class.
     *
     * @var string
     */
    public $nodeClass = 'TCMSBreadcrumbNaviItem';

    /**
     * page id.
     *
     * @var string|null
     */
    public $pageId;

    /**
     * array of nodes 0=rootNode.
     *
     * @var array
     */
    public $nodes = [];

    /* ------------------------------------------------------------------------
     *
    /* ----------------------------------------------------------------------*/
    public function __construct($pageId, $nodeClass = 'TCMSBreadcrumbNaviItem')
    {
        $this->pageId = $pageId;
        if (!empty($nodeClass)) {
            if (class_exists($nodeClass, false)) {
                $this->nodeClass = $nodeClass;
            } else {
                echo 'Error in ['.__FILE__.'] on ['.__LINE__."]: Nodeclasse [{$nodeClass}] does not exist!\n";
            }
        }
    }

    /* ------------------------------------------------------------------------
     * render the breadcrumb
    /* ----------------------------------------------------------------------*/
    public function Render($oCurrentDivisionObj)
    {
        $nodeCount = 0;
        $breadcrumb = '';
        foreach (array_keys($this->nodes) as $nodeId) {
            /* @var $this->nodes[$nodeId] TCMSBreadcrumbNaviItem */
            $breadcrumb .= $this->nodes[$nodeId]->Render($nodeCount, $oCurrentDivisionObj);
            ++$nodeCount;
        }

        return $breadcrumb;
    }

    /**
     * return tree ids from breadcrumb.
     *
     * @return array
     */
    public function GetBreadcrumbNaviIds()
    {
        $returnArray = [];

        foreach (array_keys($this->nodes) as $nodeId) {
            $returnArray[] = $this->nodes[$nodeId]->id;
        }

        return $returnArray;
    }

    /* ------------------------------------------------------------------------
     * load the breadcrumb info
    /* ----------------------------------------------------------------------*/
    public function LoadData($stopNodeArray)
    {
        if (!is_array($stopNodeArray)) {
            $stopNodeArray = [$stopNodeArray];
        }
        // fetch start node in tree from the page info
        $pageObj = $this->getPageService()->getById($this->pageId);
        /* @var $pageObj TCMSPage */
        $pageTreeId = $pageObj->GetMainTreeId();

        $done = false;
        $nodes = TCmsTree::GetNewInstance($pageTreeId);
        $this->nodes = [];

        while (!$done && ($node = MySqlLegacySupport::getInstance()->fetch_assoc($nodes))) {
            $done = in_array($node['id'], $stopNodeArray);
            $nodeObj = $this->CreateNode($node);
            $this->nodes[] = $nodeObj;
            $nodes = TCmsTree::GetNewInstance($node['parent_id']);
        }
        $this->nodes = array_reverse($this->nodes);
    }

    /* ------------------------------------------------------------------------
     * returns a node given an assoc array for input
    /* ----------------------------------------------------------------------*/
    public function CreateNode($node)
    {
        $nodeClass = $this->nodeClass;
        $nodeObj = new $nodeClass();
        $nodeObj->name = $node['name'];
        $nodeObj->page_id = $node['page_id'];
        $nodeObj->id = $node['id'];
        $nodeObj->parent_id = $node['parent_id'];
        $nodeObj->link = $node['link'];
        $nodeObj->linkTarget = $node['linkTarget'];

        return $nodeObj;
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }
}
