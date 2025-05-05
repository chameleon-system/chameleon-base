<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;

class PkgNewsletterRobinson
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var CmsPortal|null - Portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldEmail
        /** @var string - Email address */
        private string $email = '',
        // TCMSFieldVarchar
        /** @var string - Reason for blacklisting */
        private string $reason = ''
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
    public function getCmsPortal(): ?CmsPortal
    {
        return $this->cmsPortal;
    }

    public function setCmsPortal(?CmsPortal $cmsPortal): self
    {
        $this->cmsPortal = $cmsPortal;

        return $this;
    }

    // TCMSFieldEmail
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    // TCMSFieldVarchar
    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
