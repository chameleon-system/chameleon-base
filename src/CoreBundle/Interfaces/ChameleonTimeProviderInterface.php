<?php

namespace ChameleonSystem\CoreBundle\Interfaces;

interface ChameleonTimeProviderInterface
{
    public function getTime(): int;

    public function getDateTime(\DateTimeZone $timeZone): \DateTime;
}
