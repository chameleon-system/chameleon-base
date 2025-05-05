<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgNewsletterCampaign
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Campaign source (utm_source) */
        private string $utmSource = '',
        // TCMSFieldVarchar
        /** @var string - Campaign medium (utm_medium) */
        private string $utmMedium = 'email',
        // TCMSFieldVarchar
        /** @var string - Campaign content (utm_content) */
        private string $utmContent = '',
        // TCMSFieldVarchar
        /** @var string - Campaign name (utm_campaign) */
        private string $utmCampaign = '',
        // TCMSFieldVarchar
        /** @var string - Newsletter title */
        private string $name = '',
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Newlsetter template page */
        private ?CmsTree $cmsTreeNode = null,
        // TCMSFieldBoolean
        /** @var bool - Newsletter queue active */
        private bool $active = false,
        // TCMSFieldVarchar
        /** @var string - Subject */
        private string $subject = '',
        // TCMSFieldLookup
        /** @var CmsPortal|null - Portal */
        private ?CmsPortal $cmsPortal = null,
        // TCMSFieldPropertyTable
        /** @var Collection<int, PkgNewsletterQueue> - Queue items */
        private Collection $pkgNewsletterQueueCollection = new ArrayCollection(),
        // TCMSFieldText
        /** @var string - Content text */
        private string $contentPlain = '',
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - Desired shipping time */
        private ?\DateTime $queueDate = new \DateTime(),
        // TCMSFieldNewsletterCampaignStatistics
        /** @var string - Send status */
        private string $sendStatistics = '',
        // TCMSFieldDateTime
        /** @var \DateTime|null - Start of shipping */
        private ?\DateTime $sendStartDate = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - End of shipping */
        private ?\DateTime $sendEndDate = null,
        // TCMSFieldBoolean
        /** @var bool - Generate user-specific newsletters */
        private bool $generateUserDependingNewsletter = false,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, PkgNewsletterGroup> - Recipient list */
        private Collection $pkgNewsletterGroupCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Enable Google Analytics tagging */
        private bool $googleAnalyticsActive = false
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
    public function getUtmSource(): string
    {
        return $this->utmSource;
    }

    public function setUtmSource(string $utmSource): self
    {
        $this->utmSource = $utmSource;

        return $this;
    }

    // TCMSFieldVarchar
    public function getUtmMedium(): string
    {
        return $this->utmMedium;
    }

    public function setUtmMedium(string $utmMedium): self
    {
        $this->utmMedium = $utmMedium;

        return $this;
    }

    // TCMSFieldVarchar
    public function getUtmContent(): string
    {
        return $this->utmContent;
    }

    public function setUtmContent(string $utmContent): self
    {
        $this->utmContent = $utmContent;

        return $this;
    }

    // TCMSFieldVarchar
    public function getUtmCampaign(): string
    {
        return $this->utmCampaign;
    }

    public function setUtmCampaign(string $utmCampaign): self
    {
        $this->utmCampaign = $utmCampaign;

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

    // TCMSFieldTreeNode
    public function getCmsTreeNode(): ?CmsTree
    {
        return $this->cmsTreeNode;
    }

    public function setCmsTreeNode(?CmsTree $cmsTreeNode): self
    {
        $this->cmsTreeNode = $cmsTreeNode;

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

    // TCMSFieldVarchar
    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

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

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, PkgNewsletterQueue>
     */
    public function getPkgNewsletterQueueCollection(): Collection
    {
        return $this->pkgNewsletterQueueCollection;
    }

    public function addPkgNewsletterQueueCollection(PkgNewsletterQueue $pkgNewsletterQueue): self
    {
        if (!$this->pkgNewsletterQueueCollection->contains($pkgNewsletterQueue)) {
            $this->pkgNewsletterQueueCollection->add($pkgNewsletterQueue);
            $pkgNewsletterQueue->setPkgNewsletterCampaign($this);
        }

        return $this;
    }

    public function removePkgNewsletterQueueCollection(PkgNewsletterQueue $pkgNewsletterQueue): self
    {
        if ($this->pkgNewsletterQueueCollection->removeElement($pkgNewsletterQueue)) {
            // set the owning side to null (unless already changed)
            if ($pkgNewsletterQueue->getPkgNewsletterCampaign() === $this) {
                $pkgNewsletterQueue->setPkgNewsletterCampaign(null);
            }
        }

        return $this;
    }

    // TCMSFieldText
    public function getContentPlain(): string
    {
        return $this->contentPlain;
    }

    public function setContentPlain(string $contentPlain): self
    {
        $this->contentPlain = $contentPlain;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getQueueDate(): ?\DateTime
    {
        return $this->queueDate;
    }

    public function setQueueDate(?\DateTime $queueDate): self
    {
        $this->queueDate = $queueDate;

        return $this;
    }

    // TCMSFieldNewsletterCampaignStatistics
    public function getSendStatistics(): string
    {
        return $this->sendStatistics;
    }

    public function setSendStatistics(string $sendStatistics): self
    {
        $this->sendStatistics = $sendStatistics;

        return $this;
    }

    // TCMSFieldDateTime
    public function getSendStartDate(): ?\DateTime
    {
        return $this->sendStartDate;
    }

    public function setSendStartDate(?\DateTime $sendStartDate): self
    {
        $this->sendStartDate = $sendStartDate;

        return $this;
    }

    // TCMSFieldDateTime
    public function getSendEndDate(): ?\DateTime
    {
        return $this->sendEndDate;
    }

    public function setSendEndDate(?\DateTime $sendEndDate): self
    {
        $this->sendEndDate = $sendEndDate;

        return $this;
    }

    // TCMSFieldBoolean
    public function isGenerateUserDependingNewsletter(): bool
    {
        return $this->generateUserDependingNewsletter;
    }

    public function setGenerateUserDependingNewsletter(bool $generateUserDependingNewsletter): self
    {
        $this->generateUserDependingNewsletter = $generateUserDependingNewsletter;

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

    // TCMSFieldBoolean
    public function isGoogleAnalyticsActive(): bool
    {
        return $this->googleAnalyticsActive;
    }

    public function setGoogleAnalyticsActive(bool $googleAnalyticsActive): self
    {
        $this->googleAnalyticsActive = $googleAnalyticsActive;

        return $this;
    }
}
