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
     * Returns the current DateTime as a DateTime Object. If you provide no time zone, then the systems current timezone
     * will be used.
     *
     * @param \DateTimeZone|null $timeZone
     *
     * @return \DateTime
     */
    public function getDateTime(?\DateTimeZone $timeZone = null): \DateTime;
}
