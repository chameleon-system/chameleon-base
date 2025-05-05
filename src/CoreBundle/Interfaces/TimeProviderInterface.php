<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Interfaces;

interface TimeProviderInterface
{
    public function getUnixTimestamp(): int;

    /**
     * @param \DateTimeZone|null $timeZone - will use the systems current timezone if null is passed
     */
    public function getDateTime(?\DateTimeZone $timeZone = null): \DateTime;
}
