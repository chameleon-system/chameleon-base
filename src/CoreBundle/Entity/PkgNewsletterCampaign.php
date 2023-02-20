<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterCampaign {
  public function __construct(
    private string|null $id = null,
    private int|null $cmsident = null,
          
    // TCMSFieldLookup
/** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal|null - Portal */
private \ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal = null,
/** @var null|string - Portal */
private ?string $cmsPortalId = null
, 
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
/** @var \ChameleonSystem\CoreBundle\Entity\CmsTree - Newlsetter template page */
private \ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeNodeId = null, 
    // TCMSFieldBoolean
/** @var bool - Newsletter queue active */
private bool $active = false, 
    // TCMSFieldVarchar
/** @var string - Subject */
private string $subject = '', 
    // TCMSFieldPropertyTable
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterQueue[] - Queue items */
private \Doctrine\Common\Collections\Collection $pkgNewsletterQueueCollection = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldText
/** @var string - Content text */
private string $contentPlain = '', 
    // TCMSFieldDateTimeNow
/** @var \DateTime|null - Desired shipping time */
private \DateTime|null $queueDate = null, 
    // TCMSFieldNewsletterCampaignStatistics
/** @var string - Send status */
private string $sendStatistics = '', 
    // TCMSFieldDateTime
/** @var \DateTime|null - Start of shipping */
private \DateTime|null $sendStartDate = null, 
    // TCMSFieldDateTime
/** @var \DateTime|null - End of shipping */
private \DateTime|null $sendEndDate = null, 
    // TCMSFieldBoolean
/** @var bool - Generate user-specific newsletters */
private bool $generateUserDependingNewsletter = false, 
    // TCMSFieldLookupMultiselect
/** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] - Recipient list */
private \Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt = new \Doctrine\Common\Collections\ArrayCollection(), 
    // TCMSFieldBoolean
/** @var bool - Enable Google Analytics tagging */
private bool $googleAnalyticsActive = false  ) {}

  public function getId(): ?string
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
public function getCmsTreeNodeId(): \ChameleonSystem\CoreBundle\Entity\CmsTree|null
{
    return $this->cmsTreeNodeId;
}
public function setCmsTreeNodeId(\ChameleonSystem\CoreBundle\Entity\CmsTree|null $cmsTreeNodeId): self
{
    $this->cmsTreeNodeId = $cmsTreeNodeId;

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
public function getCmsPortal(): \ChameleonSystem\CoreBundle\Entity\CmsPortal|null
{
    return $this->cmsPortal;
}
public function setCmsPortal(\ChameleonSystem\CoreBundle\Entity\CmsPortal|null $cmsPortal): self
{
    $this->cmsPortal = $cmsPortal;
    $this->cmsPortalId = $cmsPortal?->getId();

    return $this;
}
public function getCmsPortalId(): ?string
{
    return $this->cmsPortalId;
}
public function setCmsPortalId(?string $cmsPortalId): self
{
    $this->cmsPortalId = $cmsPortalId;
    // todo - load new id
    //$this->cmsPortalId = $?->getId();

    return $this;
}



  
    // TCMSFieldPropertyTable
public function getPkgNewsletterQueueCollection(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgNewsletterQueueCollection;
}
public function setPkgNewsletterQueueCollection(\Doctrine\Common\Collections\Collection $pkgNewsletterQueueCollection): self
{
    $this->pkgNewsletterQueueCollection = $pkgNewsletterQueueCollection;

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
public function getQueueDate(): \DateTime|null
{
    return $this->queueDate;
}
public function setQueueDate(\DateTime|null $queueDate): self
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
public function getSendStartDate(): \DateTime|null
{
    return $this->sendStartDate;
}
public function setSendStartDate(\DateTime|null $sendStartDate): self
{
    $this->sendStartDate = $sendStartDate;

    return $this;
}


  
    // TCMSFieldDateTime
public function getSendEndDate(): \DateTime|null
{
    return $this->sendEndDate;
}
public function setSendEndDate(\DateTime|null $sendEndDate): self
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
public function getPkgNewsletterGroupMlt(): \Doctrine\Common\Collections\Collection
{
    return $this->pkgNewsletterGroupMlt;
}
public function setPkgNewsletterGroupMlt(\Doctrine\Common\Collections\Collection $pkgNewsletterGroupMlt): self
{
    $this->pkgNewsletterGroupMlt = $pkgNewsletterGroupMlt;

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
