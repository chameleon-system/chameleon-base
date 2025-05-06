<?php

namespace ChameleonSystem\TrackViewsBundle\Entity;

use ChameleonSystem\ExtranetBundle\Entity\DataExtranetUser;

class PkgTrackObjectHistory
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - */
        private string $tableName = '',
        // TCMSFieldVarchar
        /** @var string - */
        private string $ownerId = '',
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - */
        private ?\DateTime $datecreated = new \DateTime(),
        // TCMSFieldLookup
        /** @var DataExtranetUser|null - */
        private ?DataExtranetUser $dataExtranetUser = null,
        // TCMSFieldVarchar
        /** @var string - */
        private string $ip = '',
        // TCMSFieldVarchar
        /** @var string - */
        private string $requestChecksum = '',
        // TCMSFieldLookupParentID
        /** @var PkgTrackObject|null - */
        private ?PkgTrackObject $pkgTrackObject = null,
        // TCMSFieldBoolean
        /** @var bool - */
        private bool $itemCounted = false
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

    // TCMSFieldLookup
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

        return $this;
    }

    // TCMSFieldVarchar
    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRequestChecksum(): string
    {
        return $this->requestChecksum;
    }

    public function setRequestChecksum(string $requestChecksum): self
    {
        $this->requestChecksum = $requestChecksum;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getPkgTrackObject(): ?PkgTrackObject
    {
        return $this->pkgTrackObject;
    }

    public function setPkgTrackObject(?PkgTrackObject $pkgTrackObject): self
    {
        $this->pkgTrackObject = $pkgTrackObject;

        return $this;
    }

    // TCMSFieldBoolean
    public function isItemCounted(): bool
    {
        return $this->itemCounted;
    }

    public function setItemCounted(bool $itemCounted): self
    {
        $this->itemCounted = $itemCounted;

        return $this;
    }
}
