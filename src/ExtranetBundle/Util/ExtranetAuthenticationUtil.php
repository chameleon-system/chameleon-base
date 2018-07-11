<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\Util;

use ChameleonSystem\ExtranetBundle\objects\ExtranetUserConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ExtranetAuthenticationUtil implements ExtranetAuthenticationUtilInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastLoginName()
    {
        $request = $this->getCurrentRequest();

        if (null === $request) {
            return '';
        }

        return $request->request->get(ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME, '');
    }

    /**
     * @return null|Request
     */
    private function getCurrentRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
