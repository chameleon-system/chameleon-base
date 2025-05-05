<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use ChameleonSystem\ExtranetBundle\Entity\DataExtranetGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgNewsletterGroup
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Logo header image of newsletter */
        private ?CmsMedia $logoHeader = null,
        // TCMSFieldVarchar
        /** @var string - From (name) */
        private string $fromName = '',
        // TCMSFieldEmail
        /** @var string - Reply email address */
        private string $replyEmail = '',
        // TCMSFieldVarchar
        /** @var string - Name of the recipient list */
        private string $name = '',
        // TCMSFieldEmail
        /** @var string - From (email address) */
        private string $fromEmail = '',
        // TCMSFieldWYSIWYG
        /** @var string - Imprint */
        private string $imprint = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldBoolean
        /** @var bool - Send to all newsletter users */
        private bool $includeAllNewsletterUsers = false,
        // TCMSFieldBoolean
        /** @var bool - Newsletter users without assignment to a newsletter group */
        private bool $includeNewsletterUserNotAssignedToAnyGroup = false,
        // TCMSFieldBoolean
        /** @var bool - Include all newsletter users WITHOUT extranet account in the list */
        private bool $includeAllNewsletterUsersWithNoExtranetAccount = false,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, DataExtranetGroup> - Send to users with following extranet groups */
        private Collection $dataExtranetGroupCollection = new ArrayCollection()
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

    // TCMSFieldExtendedLookupMedia
    public function getLogoHeader(): ?CmsMedia
    {
        return $this->logoHeader;
    }

    public function setLogoHeader(?CmsMedia $logoHeader): self
    {
        $this->logoHeader = $logoHeader;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): self
    {
        $this->fromName = $fromName;

        return $this;
    }

    // TCMSFieldEmail
    public function getReplyEmail(): string
    {
        return $this->replyEmail;
    }

    public function setReplyEmail(string $replyEmail): self
    {
        $this->replyEmail = $replyEmail;

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

    // TCMSFieldEmail
    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function setFromEmail(string $fromEmail): self
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getImprint(): string
    {
        return $this->imprint;
    }

    public function setImprint(string $imprint): self
    {
        $this->imprint = $imprint;

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

    // TCMSFieldBoolean
    public function isIncludeAllNewsletterUsers(): bool
    {
        return $this->includeAllNewsletterUsers;
    }

    public function setIncludeAllNewsletterUsers(bool $includeAllNewsletterUsers): self
    {
        $this->includeAllNewsletterUsers = $includeAllNewsletterUsers;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIncludeNewsletterUserNotAssignedToAnyGroup(): bool
    {
        return $this->includeNewsletterUserNotAssignedToAnyGroup;
    }

    public function setIncludeNewsletterUserNotAssignedToAnyGroup(bool $includeNewsletterUserNotAssignedToAnyGroup
    ): self {
        $this->includeNewsletterUserNotAssignedToAnyGroup = $includeNewsletterUserNotAssignedToAnyGroup;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIncludeAllNewsletterUsersWithNoExtranetAccount(): bool
    {
        return $this->includeAllNewsletterUsersWithNoExtranetAccount;
    }

    public function setIncludeAllNewsletterUsersWithNoExtranetAccount(
        bool $includeAllNewsletterUsersWithNoExtranetAccount
    ): self {
        $this->includeAllNewsletterUsersWithNoExtranetAccount = $includeAllNewsletterUsersWithNoExtranetAccount;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, DataExtranetGroup>
     */
    public function getDataExtranetGroupCollection(): Collection
    {
        return $this->dataExtranetGroupCollection;
    }

    public function addDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if (!$this->dataExtranetGroupCollection->contains($dataExtranetGroupMlt)) {
            $this->dataExtranetGroupCollection->add($dataExtranetGroupMlt);
            $dataExtranetGroupMlt->set($this);
        }

        return $this;
    }

    public function removeDataExtranetGroupCollection(DataExtranetGroup $dataExtranetGroupMlt): self
    {
        if ($this->dataExtranetGroupCollection->removeElement($dataExtranetGroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($dataExtranetGroupMlt->get() === $this) {
                $dataExtranetGroupMlt->set(null);
            }
        }

        return $this;
    }
}
