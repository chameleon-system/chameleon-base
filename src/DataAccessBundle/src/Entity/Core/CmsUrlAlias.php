<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;

class CmsUrlAlias
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsPortal|null - Belongs to portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldVarchar
        /** @var string - Name / notes */
        private string $name = '',
        // TCMSFieldURL
        /** @var string - Source */
        private string $sourceUrl = '',
        // TCMSFieldBoolean
        /** @var bool - Exact match of the source path */
        private bool $exactMatch = true,
        // TCMSFieldVarchar
        /** @var string - Target */
        private string $targetUrl = '',
        // TCMSFieldText
        /** @var string - Ignore these parameters */
        private string $ignoreParameter = '',
        // TCMSFieldText
        /** @var string - Parameter mapping */
        private string $parameterMapping = '',
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Created by */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldCreatedTimestamp
        /** @var \DateTime|null - Creation date */
        private ?\DateTime $datecreated = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Expiry date */
        private ?\DateTime $expirationDate = null,
        // TCMSFieldBoolean
        /** @var bool - Active */
        private bool $active = true
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

    // TCMSFieldURL
    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): self
    {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    // TCMSFieldBoolean
    public function isExactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function setExactMatch(bool $exactMatch): self
    {
        $this->exactMatch = $exactMatch;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl): self
    {
        $this->targetUrl = $targetUrl;

        return $this;
    }

    // TCMSFieldText
    public function getIgnoreParameter(): string
    {
        return $this->ignoreParameter;
    }

    public function setIgnoreParameter(string $ignoreParameter): self
    {
        $this->ignoreParameter = $ignoreParameter;

        return $this;
    }

    // TCMSFieldText
    public function getParameterMapping(): string
    {
        return $this->parameterMapping;
    }

    public function setParameterMapping(string $parameterMapping): self
    {
        $this->parameterMapping = $parameterMapping;

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

    // TCMSFieldCreatedTimestamp
    public function getDatecreated(): ?\DateTime
    {
        return $this->datecreated;
    }

    public function setDatecreated(?\DateTime $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }

    // TCMSFieldDateTime
    public function getExpirationDate(): ?\DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTime $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    // TCMSFieldBoolean
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
