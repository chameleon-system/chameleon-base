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

use ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTreeSingleSelect\NavigationTreeSingleSelect;
use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * {@inheritdoc}
 */
class NavigationTreeSingleSelectWysiwyg extends NavigationTreeSingleSelect
{
    public function __construct(
        InputFilterUtilInterface $inputFilterUtil,
        UrlUtil $urlUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        TranslatorInterface $translator,
        \TTools $tools,
        \TGlobal $global,
        FieldTranslationUtil $fieldTranslationUtil,
        BackendSessionInterface $backendSession,
        Connection $dbConnection,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct(
            $inputFilterUtil,
            $urlUtil,
            $backendTreeNodeFactory,
            $translator,
            $tools,
            $global,
            $fieldTranslationUtil,
            $backendSession,
            $dbConnection
        );
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        parent::Accept($visitor, $cachingEnabled, $cacheTriggerManager);
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }
        $visitor->SetMappedValue('CKEditorFuncNum', $request->query->getInt('CKEditorFuncNum'));
    }

    protected function disableSelectionWysiwyg(BackendTreeNodeDataModel $treeNodeDataModel): void
    {
        $treeNodeDataModel->setDisabled(true);
        $treeNodeDataModel->addListHtmlClass('no-checkbox');
    }

    protected function setCheckStatus(BackendTreeNodeDataModel $treeNodeDataModel, $nodeId): void
    {
    }
}
