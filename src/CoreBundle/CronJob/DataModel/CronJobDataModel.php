<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\CronJob\DataModel;

readonly class CronJobDataModel
{
    public function __construct(
        private string $id,
        private string $name,
        private bool $active,
        private CronJobScheduleDataModel $cronJobScheduleDataModel,
        private string $cmsEditUrl
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCronJobScheduleDataModel(): CronJobScheduleDataModel
    {
        return $this->cronJobScheduleDataModel;
    }

    public function getCmsEditUrl(): string
    {
        return $this->cmsEditUrl;
    }
}
