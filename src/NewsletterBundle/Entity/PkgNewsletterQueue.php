<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

class PkgNewsletterQueue
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookup
        /** @var PkgNewsletterUser|null - Newsletter subscriber */
        private ?PkgNewsletterUser $pkgNewsletterUser = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Shipped on */
        private ?\DateTime $dateSent = null,
        // TCMSFieldLookupParentID
        /** @var PkgNewsletterCampaign|null - Newsletter */
        private ?PkgNewsletterCampaign $pkgNewsletterCampaign = null
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
    public function getPkgNewsletterUser(): ?PkgNewsletterUser
    {
        return $this->pkgNewsletterUser;
    }

    public function setPkgNewsletterUser(?PkgNewsletterUser $pkgNewsletterUser): self
    {
        $this->pkgNewsletterUser = $pkgNewsletterUser;

        return $this;
    }

    // TCMSFieldDateTime
    public function getDateSent(): ?\DateTime
    {
        return $this->dateSent;
    }

    public function setDateSent(?\DateTime $dateSent): self
    {
        $this->dateSent = $dateSent;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getPkgNewsletterCampaign(): ?PkgNewsletterCampaign
    {
        return $this->pkgNewsletterCampaign;
    }

    public function setPkgNewsletterCampaign(?PkgNewsletterCampaign $pkgNewsletterCampaign): self
    {
        $this->pkgNewsletterCampaign = $pkgNewsletterCampaign;

        return $this;
    }
}
