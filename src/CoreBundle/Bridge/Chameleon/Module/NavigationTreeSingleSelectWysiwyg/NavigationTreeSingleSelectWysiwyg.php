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
use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use TGlobal;
use TTools;

/**
 * {@inheritdoc}
 */
class NavigationTreeSingleSelectWysiwyg extends NavigationTreeSingleSelect
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        InputFilterUtilInterface $inputFilterUtil,
        UrlUtil $urlUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        TranslatorInterface $translator,
        TTools $tools,
        TGlobal $global,
        FieldTranslationUtil $fieldTranslationUtil,
        LanguageServiceInterface $languageService,
        RequestStack $requestStack
    ) {
        parent::__construct(
            $inputFilterUtil,
            $urlUtil,
            $backendTreeNodeFactory,
            $translator,
            $tools,
            $global,
            $fieldTranslationUtil,
            $languageService
        );
        $this->requestStack = $requestStack;
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
