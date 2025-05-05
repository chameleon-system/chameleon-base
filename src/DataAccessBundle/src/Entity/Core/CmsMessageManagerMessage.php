<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;

class CmsMessageManagerMessage
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsPortal|null - Belongs to portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - Code */
        private string $name = '',
        // TCMSFieldLookup
        /** @var CmsMessageManagerMessageType|null - Message type */
        private ?CmsMessageManagerMessageType $cmsMessageManagerMessageType = null,
        // TCMSFieldText
        /** @var string - Message description */
        private string $description = '',
        // TCMSFieldText
        /** @var string - Message */
        private string $message = '',
        // TCMSFieldOption
        /** @var string - Type */
        private string $messageLocationType = 'Core',
        // TCMSFieldVarchar
        /** @var string - View */
        private string $messageView = 'standard'
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

    // TCMSFieldLookupParentID
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

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
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    // TCMSFieldOption
    public function getMessageLocationType(): string
    {
        return $this->messageLocationType;
    }

    public function setMessageLocationType(string $messageLocationType): self
    {
        $this->messageLocationType = $messageLocationType;

        return $this;
    }

    // TCMSFieldVarchar
    public function getMessageView(): string
    {
        return $this->messageView;
    }

    public function setMessageView(string $messageView): self
    {
        $this->messageView = $messageView;

        return $this;
    }
}
