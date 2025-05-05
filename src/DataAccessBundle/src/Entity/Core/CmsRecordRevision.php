<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreTableConfiguration\CmsTblConf;

class CmsRecordRevision
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsRecordRevision|null - belongs to revision */
        private ?CmsRecordRevision $cmsRecordRevision = null,
        // TCMSFieldLookup
        /** @var CmsTblConf|null - Table */
        private ?CmsTblConf $cmsTblConf = null,
        // TCMSFieldVarchar
        /** @var string - Record ID */
        private string $recordid = '',
        // TCMSFieldVarchar
        /** @var string - Title */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldNumber
        /** @var int - Version number */
        private int $revisionNr = 0,
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Editor */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldTimestamp
        /** @var \DateTime|null - Created on */
        private ?\DateTime $createTimestamp = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Time of last activation */
        private ?\DateTime $lastActiveTimestamp = null,
        // TCMSFieldText
        /** @var string - Serialized record */
        private string $data = ''
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

    // TCMSFieldLookup
    public function getCmsRecordRevision(): ?CmsRecordRevision
    {
        return $this->cmsRecordRevision;
    }

    public function setCmsRecordRevision(?CmsRecordRevision $cmsRecordRevision): self
    {
        $this->cmsRecordRevision = $cmsRecordRevision;

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

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldText
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // TCMSFieldNumber
    public function getRevisionNr(): int
    {
        return $this->revisionNr;
    }

    public function setRevisionNr(int $revisionNr): self
    {
        $this->revisionNr = $revisionNr;

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
    public function getCreateTimestamp(): ?\DateTime
    {
        return $this->createTimestamp;
    }

    public function setCreateTimestamp(?\DateTime $createTimestamp): self
    {
        $this->createTimestamp = $createTimestamp;

        return $this;
    }

    // TCMSFieldDateTime
    public function getLastActiveTimestamp(): ?\DateTime
    {
        return $this->lastActiveTimestamp;
    }

    public function setLastActiveTimestamp(?\DateTime $lastActiveTimestamp): self
    {
        $this->lastActiveTimestamp = $lastActiveTimestamp;

        return $this;
    }

    // TCMSFieldText
    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }
}
