<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\Service;

use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ExtranetUserStaticProvider implements ExtranetUserProviderInterface
{
    private ?\TdbDataExtranetUser $staticUser = null;

    private ExtranetUserProviderInterface $subject;

    public function __construct(ExtranetUserProviderInterface $subject)
    {
        $this->subject = $subject;
    }

    public function setStaticUser(\TdbDataExtranetUser $user): void
    {
        $this->staticUser = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUser()
    {
        if (null !== $this->staticUser) {
            return $this->staticUser;
        }

        return $this->subject->getActiveUser();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if (null !== $this->staticUser) {
            $this->staticUser = null;

            return;
        }
        $this->subject->reset();
    }

}
