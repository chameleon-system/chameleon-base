<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TCMSViewPortManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack|null $requestStack can be null to avoid a BC break
     * @param InputFilterUtilInterface|null $inputFilterUtil can be null to avoid a BC break
     */
    public function __construct(?RequestStack $requestStack = null, ?InputFilterUtilInterface $inputFilterUtil = null)
    {
        if (null === $requestStack) {
            $this->requestStack = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack');
        } else {
            $this->requestStack = $requestStack;
        }
        if (null === $inputFilterUtil) {
            $this->inputFilterUtil = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.input_filter');
        } else {
            $this->inputFilterUtil = $inputFilterUtil;
        }
    }

    /**
     * get the correct view port tag value from session or from url parameter.
     *
     * @return string
     */
    public function getViewPortContent()
    {
        if (true === $this->isDesktopViewPort()) {
            return 'width=1024';
        } else {
            return 'width=device-width, initial-scale=1.0';
        }
    }

    /**
     * Get the correct view port typ from session or from url parameter
     * If returns true for desktop view and false for mobile view.
     *
     * @return bool
     */
    public function isDesktopViewPort()
    {
        $bDesktopViewPort = $this->inputFilterUtil->getFilteredInput('showDesktopMode', null, false, TCMSUserInput::FILTER_URL_INTERNAL);
        $request = $this->requestStack->getCurrentRequest();
        if (false === $request->hasSession()) {
            return false;
        }
        $oSession = $request->getSession();
        if (null !== $bDesktopViewPort) {
            $bDesktopViewPort = '1' === $bDesktopViewPort;
            $oSession->set('bDesktopViewPort', $bDesktopViewPort);
        } elseif (true === $oSession->has('bDesktopViewPort')) {
            $bDesktopViewPort = $oSession->get('bDesktopViewPort');
        } else {
            $oSession->set('bDesktopViewPort', false);
            $bDesktopViewPort = false;
        }

        return $bDesktopViewPort;
    }
}
