<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreConfig\CmsConfig;

class CmsMessageManagerBackendMessage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsMessageManagerMessageType|null - Message type */
        private ?CmsMessageManagerMessageType $cmsMessageManagerMessageType = null,
        // TCMSFieldText
        /** @var string - Message */
        private string $message = '',
        // TCMSFieldLookupParentID
        /** @var CmsConfig|null - Belongs to CMS config */
        private ?CmsConfig $cmsConfig = null,
        // TCMSFieldVarchar
        /** @var string - Code */
        private string $name = '',
        // TCMSFieldText
        /** @var string - Message description */
        private string $description = ''
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
    public function getCmsMessageManagerMessageType(): ?CmsMessageManagerMessageType
    {
        return $this->cmsMessageManagerMessageType;
    }

    public function setCmsMessageManagerMessageType(?CmsMessageManagerMessageType $cmsMessageManagerMessageType): self
    {
        $this->cmsMessageManagerMessageType = $cmsMessageManagerMessageType;

        return $this;
    }

    // TCMSFieldText
    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsConfig(): ?CmsConfig
    {
        return $this->cmsConfig;
    }

    public function setCmsConfig(?CmsConfig $cmsConfig): self
    {
        $this->cmsConfig = $cmsConfig;

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
}
