<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ActivePageServiceInitializer.
 */
class ActivePageServiceInitializer implements ActivePageServiceInitializerInterface
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, InputFilterUtilInterface $inputFilterUtil)
    {
        $this->container = $container;
        $this->inputFilterUtil = $inputFilterUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ActivePageServiceInterface $activePageService)
    {
        $pagedef = null;
        $referrerPageId = null;
        $requestInfoService = $this->getRequestInfoService();
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)
            && true === $requestInfoService->isCmsTemplateEngineEditMode()
        ) {
            $pagedef = $this->inputFilterUtil->getFilteredInput('pagedef', null, false, \TCMSUserInput::FILTER_FILENAME);
            $referrerPageId = $this->inputFilterUtil->getFilteredInput('refererPageId', null, false, \TCMSUserInput::FILTER_FILENAME);
        }
        $activePageService->setActivePage($pagedef, $referrerPageId);
    }

    /**
     * @return RequestInfoServiceInterface
     */
    private function getRequestInfoService()
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }
}
