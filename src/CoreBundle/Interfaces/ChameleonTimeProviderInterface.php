<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

interface ChameleonTimeProviderInterface
{
    public function getTime(): int;

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