<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\TimeProviderInterface;

final class SystemTimeProvider implements TimeProviderInterface
{
    public function getTime(): int
    {
        return time();
    }

    public function getDateTime(?\DateTimeZone $timeZone = null): \DateTime
    {
        return new \DateTime('now', $timeZone);
    }

}