<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTreeSingleSelectWysiwyg;

use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTreeSingleSelect\NavigationTreeSingleSelect;


/**
 * {@inheritdoc}
 *
 *   !!!!!!!!  W-O-R-K  I-N  P-R-O-C-E-S-S !!!!!!!!
 *
 */
class NavigationTreeSingleSelectWysiwyg extends NavigationTreeSingleSelect
{
    /**
     * @var Connection
     */
    private $dbConnection;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var BackendTreeNodeFactory
     */
    private $backendTreeNodeFactory;

    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory
    ) {
        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
    }

    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
//        parent::Accept();
        $visitor->SetMappedValue('CKEditorFuncNum', $this->getRequest()->query->getInt('CKEditorFuncNum'));

        return $this->data;
    }

    /**
     * @param string       $fieldName
     * @param \TCMSTreeNode $oNode
     *
     * @return string
     */
    protected function getOnClick($fieldName, $oNode)
    {
        $treeNodeConnection = $oNode->GetActivePageTreeConnectionForTree();
        if (false === $treeNodeConnection) {
            return '';
        }
        $page = $treeNodeConnection->GetFieldContid();
        if (null === $page) {
            return '';
        }
        $name = \TGlobal::OutJS($oNode->GetName());
        $pageId = \TGlobal::OutJS($page->id);
        $nodeId = \TGlobal::OutJS($oNode->id);

        return "chooseTreeNode('{$pageId}','{$nodeId}','{$name}');";
    }

    /**
     * @return Request|null
     */
    private function getRequest()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
    }
}
