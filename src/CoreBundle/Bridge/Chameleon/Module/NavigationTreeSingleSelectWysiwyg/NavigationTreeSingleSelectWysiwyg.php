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
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use TGlobal;

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
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        TGlobal $global,
        RequestStack $requestStack
    ) {
        parent::__construct($dbConnection, $eventDispatcher, $inputFilterUtil, $backendTreeNodeFactory, $global);
        $this->requestStack = $requestStack;
    }

    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        parent::Accept($visitor, $cachingEnabled, $cacheTriggerManager);
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }
        $visitor->SetMappedValue('CKEditorFuncNum', $request->query->getInt('CKEditorFuncNum'));
    }
}
