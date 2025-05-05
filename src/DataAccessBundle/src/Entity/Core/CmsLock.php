<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;

class CmsLock
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Record ID */
        private string $recordid = '',
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Editor */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldTimestamp
        /** @var \DateTime|null - last changed by */
        private ?\DateTime $timeStamp = null,
        // TCMSFieldLookup
        /** @var CmsTblConf|null - Lock table */
        private ?CmsTblConf $cmsTblConf = null
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRecordid(): string
    {
        return $this->recordid;
    }

    public function setRecordid(string $recordid): self
    {
        $this->recordid = $recordid;

        return $this;
    }

    // TCMSFieldCMSUser
    public function getCmsUser(): ?CmsUser
    {
        return $this->cmsUser;
    }

    public function setCmsUser(?CmsUser $cmsUser): self
    {
        $this->cmsUser = $cmsUser;

        return $this;
    }

    // TCMSFieldTimestamp
    public function getTimeStamp(): ?\DateTime
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(?\DateTime $timeStamp): self
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsTblConf(): ?CmsTblConf
    {
        return $this->cmsTblConf;
    }

    public function setCmsTblConf(?CmsTblConf $cmsTblConf): self
    {
        $this->cmsTblConf = $cmsTblConf;

        return $this;
    }
}
