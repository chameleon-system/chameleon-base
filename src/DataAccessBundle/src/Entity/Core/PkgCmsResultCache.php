<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

class PkgCmsResultCache
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Owner identification */
        private string $ownerHash = '',
        // TCMSFieldVarchar
        /** @var string - Identification */
        private string $hash = '',
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - Creation date */
        private ?\DateTime $dateCreated = new \DateTime(),
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - Entry invalid from */
        private ?\DateTime $dateExpireAfter = new \DateTime(),
        // TCMSFieldText
        /** @var string - Content */
        private string $data = '',
        // TCMSFieldBoolean
        /** @var bool - Delete if invalid */
        private bool $garbageCollectWhenExpired = false
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
    public function getOwnerHash(): string
    {
        return $this->ownerHash;
    }

    public function setOwnerHash(string $ownerHash): self
    {
        $this->ownerHash = $ownerHash;

        return $this;
    }

    // TCMSFieldVarchar
    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getDateExpireAfter(): ?\DateTime
    {
        return $this->dateExpireAfter;
    }

    public function setDateExpireAfter(?\DateTime $dateExpireAfter): self
    {
        $this->dateExpireAfter = $dateExpireAfter;

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

    // TCMSFieldBoolean
    public function isGarbageCollectWhenExpired(): bool
    {
        return $this->garbageCollectWhenExpired;
    }

    public function setGarbageCollectWhenExpired(bool $garbageCollectWhenExpired): self
    {
        $this->garbageCollectWhenExpired = $garbageCollectWhenExpired;

        return $this;
    }
}
