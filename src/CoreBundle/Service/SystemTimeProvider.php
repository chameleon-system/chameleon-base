<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;

final class SystemTimeProvider implements TimeProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUnixTimestamp(): int
    {
        return time();
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTime(?\DateTimeZone $timeZone = null): \DateTime
    {
        return new \DateTime('now', $timeZone);
    }
}
