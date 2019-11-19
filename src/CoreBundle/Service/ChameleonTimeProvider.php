<?php

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\ChameleonTimeProviderInterface;

final class ChameleonTimeProvider implements ChameleonTimeProviderInterface
{
    public function getTime(): int
    {
        return time();
    }

    public function getDateTime(\DateTimeZone $timeZone): \DateTime
    {
        return new \DateTime('now', $timeZone);
    }

}