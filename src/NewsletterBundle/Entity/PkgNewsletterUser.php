<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use ChameleonSystem\ExtranetBundle\Entity\DataExtranetSalutation;
use ChameleonSystem\ExtranetBundle\Entity\DataExtranetUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgNewsletterUser
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldExtendedLookup
        /** @var DataExtranetUser|null - Belongs to customer */
        private ?DataExtranetUser $dataExtranetUser = null,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, PkgNewsletterGroup> - Subscriber of recipient lists */
        private Collection $pkgNewsletterGroupCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, PkgNewsletterConfirmation> - Confirmations */
        private Collection $pkgNewsletterConfirmationCollection = new ArrayCollection(),
        // TCMSFieldEmail
        /** @var string - Email address */
        private string $email = '',
        // TCMSFieldLookup
        /** @var DataExtranetSalutation|null - Write delete log */
        private ?DataExtranetSalutation $dataExtranetSalutation = null,
        // TCMSFieldVarchar
        /** @var string - First name */
        private string $firstname = '',
        // TCMSFieldVarchar
        /** @var string - Last name */
        private string $lastname = '',
        // TCMSFieldVarchar
        /** @var string - Company */
        private string $company = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Subscription date */
        private ?\DateTime $signupDate = null,
        // TCMSFieldVarchar
        /** @var string - Confirmation code */
        private string $optincode = '',
        // TCMSFieldBoolean
        /** @var bool - Subscription confirmed */
        private bool $optin = false,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Confirmed on */
        private ?\DateTime $optinDate = null,
        // TCMSFieldVarchar
        /** @var string - Unsubscription code */
        private string $optoutcode = ''
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

    // TCMSFieldExtendedLookup
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, PkgNewsletterGroup>
     */
    public function getPkgNewsletterGroupCollection(): Collection
    {
        return $this->pkgNewsletterGroupCollection;
    }

    public function addPkgNewsletterGroupCollection(PkgNewsletterGroup $pkgNewsletterGroupMlt): self
    {
        if (!$this->pkgNewsletterGroupCollection->contains($pkgNewsletterGroupMlt)) {
            $this->pkgNewsletterGroupCollection->add($pkgNewsletterGroupMlt);
            $pkgNewsletterGroupMlt->set($this);
        }

        return $this;
    }

    public function removePkgNewsletterGroupCollection(PkgNewsletterGroup $pkgNewsletterGroupMlt): self
    {
        if ($this->pkgNewsletterGroupCollection->removeElement($pkgNewsletterGroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($pkgNewsletterGroupMlt->get() === $this) {
                $pkgNewsletterGroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, PkgNewsletterConfirmation>
     */
    public function getPkgNewsletterConfirmationCollection(): Collection
    {
        return $this->pkgNewsletterConfirmationCollection;
    }

    public function addPkgNewsletterConfirmationCollection(PkgNewsletterConfirmation $pkgNewsletterConfirmationMlt
    ): self {
        if (!$this->pkgNewsletterConfirmationCollection->contains($pkgNewsletterConfirmationMlt)) {
            $this->pkgNewsletterConfirmationCollection->add($pkgNewsletterConfirmationMlt);
            $pkgNewsletterConfirmationMlt->set($this);
        }

        return $this;
    }

    public function removePkgNewsletterConfirmationCollection(PkgNewsletterConfirmation $pkgNewsletterConfirmationMlt
    ): self {
        if ($this->pkgNewsletterConfirmationCollection->removeElement($pkgNewsletterConfirmationMlt)) {
            // set the owning side to null (unless already changed)
            if ($pkgNewsletterConfirmationMlt->get() === $this) {
                $pkgNewsletterConfirmationMlt->set(null);
            }
        }

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

    // TCMSFieldLookup
    public function getDataExtranetSalutation(): ?DataExtranetSalutation
    {
        return $this->dataExtranetSalutation;
    }

    public function setDataExtranetSalutation(?DataExtranetSalutation $dataExtranetSalutation): self
    {
        $this->dataExtranetSalutation = $dataExtranetSalutation;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    // TCMSFieldVarchar
    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

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

    // TCMSFieldDateTime
    public function getSignupDate(): ?\DateTime
    {
        return $this->signupDate;
    }

    public function setSignupDate(?\DateTime $signupDate): self
    {
        $this->signupDate = $signupDate;

        return $this;
    }

    // TCMSFieldVarchar
    public function getOptincode(): string
    {
        return $this->optincode;
    }

    public function setOptincode(string $optincode): self
    {
        $this->optincode = $optincode;

        return $this;
    }

    // TCMSFieldBoolean
    public function isOptin(): bool
    {
        return $this->optin;
    }

    public function setOptin(bool $optin): self
    {
        $this->optin = $optin;

        return $this;
    }

    // TCMSFieldDateTime
    public function getOptinDate(): ?\DateTime
    {
        return $this->optinDate;
    }

    public function setOptinDate(?\DateTime $optinDate): self
    {
        $this->optinDate = $optinDate;

        return $this;
    }

    // TCMSFieldVarchar
    public function getOptoutcode(): string
    {
        return $this->optoutcode;
    }

    public function setOptoutcode(string $optoutcode): self
    {
        $this->optoutcode = $optoutcode;

        return $this;
    }
}
