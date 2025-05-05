<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMedia;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUser;

class CmsDocument
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Name */
        private string $name = '',
        // TCMSFieldVarchar
        /** @var string - File name */
        private string $filename = '',
        // TCMSFieldText
        /** @var string - Description */
        private string $description = '',
        // TCMSFieldBoolean
        /** @var bool - Private */
        private bool $private = false,
        // TCMSFieldBoolean
        /** @var bool - Time-limited download authorization */
        private bool $tokenProtected = false,
        // TCMSFieldTimestamp
        /** @var \DateTime|null - Last changed on */
        private ?\DateTime $timeStamp = null,
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Last changed by */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldNumber
        /** @var int - Image width */
        private int $hiddenImageWidth = 0,
        // TCMSFieldNumber
        /** @var int - Image height */
        private int $hiddenImageHeight = 0,
        // TCMSFieldNumber
        /** @var int - User downloads */
        private int $counter = 0,
        // TCMSFieldLookup
        /** @var CmsDocumentTree|null - Folder */
        private ?CmsDocumentTree $cmsDocumentTree = null,
        // TCMSFieldNumber
        /** @var int - File size */
        private int $filesize = 0,
        // TCMSFieldVarchar
        /** @var string - SEO Name */
        private string $seoName = ''
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
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    // TCMSFieldBoolean
    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    // TCMSFieldBoolean
    public function isTokenProtected(): bool
    {
        return $this->tokenProtected;
    }

    public function setTokenProtected(bool $tokenProtected): self
    {
        $this->tokenProtected = $tokenProtected;

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

    // TCMSFieldNumber
    public function getHiddenImageWidth(): int
    {
        return $this->hiddenImageWidth;
    }

    public function setHiddenImageWidth(int $hiddenImageWidth): self
    {
        $this->hiddenImageWidth = $hiddenImageWidth;

        return $this;
    }

    // TCMSFieldNumber
    public function getHiddenImageHeight(): int
    {
        return $this->hiddenImageHeight;
    }

    public function setHiddenImageHeight(int $hiddenImageHeight): self
    {
        $this->hiddenImageHeight = $hiddenImageHeight;

        return $this;
    }

    // TCMSFieldNumber
    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsDocumentTree(): ?CmsDocumentTree
    {
        return $this->cmsDocumentTree;
    }

    public function setCmsDocumentTree(?CmsDocumentTree $cmsDocumentTree): self
    {
        $this->cmsDocumentTree = $cmsDocumentTree;

        return $this;
    }

    // TCMSFieldNumber
    public function getFilesize(): int
    {
        return $this->filesize;
    }

    public function setFilesize(int $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSeoName(): string
    {
        return $this->seoName;
    }

    public function setSeoName(string $seoName): self
    {
        $this->seoName = $seoName;

        return $this;
    }
}
