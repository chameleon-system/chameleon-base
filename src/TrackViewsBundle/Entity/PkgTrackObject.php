<?php

namespace ChameleonSystem\TrackViewsBundle\Entity;

class PkgTrackObject
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldNumber
        /** @var int - */
        private int $count = 0,
        // TCMSFieldVarchar
        /** @var string - */
        private string $tableName = '',
        // TCMSFieldVarchar
        /** @var string - */
        private string $ownerId = '',
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - */
        private ?\DateTime $datecreated = new \DateTime(),
        // TCMSFieldVarchar
        /** @var string - */
        private string $timeBlock = ''
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

    // TCMSFieldNumber
    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;

        return $this;
    }

    // TCMSFieldVarchar
    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): self
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getDatecreated(): ?\DateTime
    {
        return $this->datecreated;
    }

    public function setDatecreated(?\DateTime $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTimeBlock(): string
    {
        return $this->timeBlock;
    }

    public function setTimeBlock(string $timeBlock): self
    {
        $this->timeBlock = $timeBlock;

        return $this;
    }
}
