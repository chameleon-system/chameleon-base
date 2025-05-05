<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;

class PkgRunFrontendAction
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Class */
        private string $class = '',
        // TCMSFieldVarchar
        /** @var string - */
        private string $randomKey = '',
        // TCMSFieldDateTime
        /** @var \DateTime|null - Expiry date */
        private ?\DateTime $expireDate = null,
        // TCMSFieldLookup
        /** @var CmsPortal|null - */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldText
        /** @var string - */
        private string $parameter = '',
        // TCMSFieldLookup
        /** @var CmsLanguage|null - Language */
        private ?CmsLanguage $cmsLanguage = null
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
    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRandomKey(): string
    {
        return $this->randomKey;
    }

    public function setRandomKey(string $randomKey): self
    {
        $this->randomKey = $randomKey;

        return $this;
    }

    // TCMSFieldDateTime
    public function getExpireDate(): ?\DateTime
    {
        return $this->expireDate;
    }

    public function setExpireDate(?\DateTime $expireDate): self
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

        return $this;
    }

    // TCMSFieldText
    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsLanguage(): ?CmsLanguage
    {
        return $this->cmsLanguage;
    }

    public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
    {
        $this->cmsLanguage = $cmsLanguage;

        return $this;
    }
}
