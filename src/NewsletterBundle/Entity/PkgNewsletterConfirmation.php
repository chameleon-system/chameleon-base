<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

class PkgNewsletterConfirmation
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldDateTime
        /** @var \DateTime|null - Registration date */
        private ?\DateTime $registrationDate = null,
        // TCMSFieldBoolean
        /** @var bool - Registration confirmed */
        private bool $confirmation = false,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Registration confirmed on */
        private ?\DateTime $confirmationDate = null,
        // TCMSFieldLookup
        /** @var PkgNewsletterGroup|null - Subscription to newsletter group */
        private ?PkgNewsletterGroup $pkgNewsletterGroup = null,
        // TCMSFieldVarchar
        /** @var string - Double opt-out key */
        private string $optoutKey = ''
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

    // TCMSFieldDateTime
    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?\DateTime $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    // TCMSFieldBoolean
    public function isConfirmation(): bool
    {
        return $this->confirmation;
    }

    public function setConfirmation(bool $confirmation): self
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    // TCMSFieldDateTime
    public function getConfirmationDate(): ?\DateTime
    {
        return $this->confirmationDate;
    }

    public function setConfirmationDate(?\DateTime $confirmationDate): self
    {
        $this->confirmationDate = $confirmationDate;

        return $this;
    }

    // TCMSFieldLookup
    public function getPkgNewsletterGroup(): ?PkgNewsletterGroup
    {
        return $this->pkgNewsletterGroup;
    }

    public function setPkgNewsletterGroup(?PkgNewsletterGroup $pkgNewsletterGroup): self
    {
        $this->pkgNewsletterGroup = $pkgNewsletterGroup;

        return $this;
    }

    // TCMSFieldVarchar
    public function getOptoutKey(): string
    {
        return $this->optoutKey;
    }

    public function setOptoutKey(string $optoutKey): self
    {
        $this->optoutKey = $optoutKey;

        return $this;
    }
}
